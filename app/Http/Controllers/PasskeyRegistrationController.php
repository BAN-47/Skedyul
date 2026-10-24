<?php

namespace App\Http\Controllers;

use App\Models\WebauthnCredential;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use ParagonIE\ConstantTime\Base64UrlSafe;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Webauthn\AuthenticatorAttestationResponse;
use Webauthn\AuthenticatorAttestationResponseValidator;
use Webauthn\AttestationStatement\AttestationStatementSupportManager;
use Webauthn\CeremonyStep\CeremonyStepManagerFactory;
use Webauthn\AuthenticatorSelectionCriteria;
use Webauthn\Denormalizer\WebauthnSerializerFactory;
use Webauthn\PublicKeyCredential;
use Webauthn\PublicKeyCredentialCreationOptions;
use Webauthn\PublicKeyCredentialDescriptor;
use Webauthn\PublicKeyCredentialParameters;
use Webauthn\PublicKeyCredentialRpEntity;
use Webauthn\PublicKeyCredentialUserEntity;

class PasskeyRegistrationController extends Controller
{
    private function serializer(): \Symfony\Component\Serializer\SerializerInterface
    {
        return (new WebauthnSerializerFactory(
            AttestationStatementSupportManager::create()
        ))->create();
    }

    public function options(Request $request): JsonResponse
    {
        $user = $request->user();
        $rpId = (string) config('webauthn.rp_id');
        $rpName = (string) config('webauthn.rp_name');

        abort_if($rpId === '' || $rpName === '', 500, 'Passkey site settings are incomplete.');

        $excludeCredentials = WebauthnCredential::query()
            ->where('usr_id', $user->usr_id)
            ->get(['credential_id', 'transports'])
            ->map(fn (WebauthnCredential $credential) => PublicKeyCredentialDescriptor::create(
                PublicKeyCredentialDescriptor::CREDENTIAL_TYPE_PUBLIC_KEY,
                Base64UrlSafe::decodeNoPadding($credential->credential_id),
                $credential->transports ?? []
            ))
            ->all();

        // The stable user handle is an opaque, non-email identifier and is within WebAuthn's 64-byte limit.
        $userHandle = str_replace('-', '', (string) $user->usr_id);

        $options = PublicKeyCredentialCreationOptions::create(
            PublicKeyCredentialRpEntity::create($rpName, $rpId),
            PublicKeyCredentialUserEntity::create(
                (string) $user->usr_email,
                $userHandle,
                (string) ($user->usr_name ?: $user->usr_email)
            ),
            random_bytes(32),
            [
                PublicKeyCredentialParameters::createPk(-7),   // ES256
                PublicKeyCredentialParameters::createPk(-257), // RS256
            ],
            AuthenticatorSelectionCriteria::create(
                null,
                AuthenticatorSelectionCriteria::USER_VERIFICATION_REQUIREMENT_REQUIRED,
                AuthenticatorSelectionCriteria::RESIDENT_KEY_REQUIREMENT_REQUIRED
            ),
            PublicKeyCredentialCreationOptions::ATTESTATION_CONVEYANCE_PREFERENCE_NONE,
            $excludeCredentials,
            60000
        );

        $serializer = $this->serializer();

        $optionsJson = $serializer->serialize($options, 'json', [
            AbstractObjectNormalizer::SKIP_NULL_VALUES => true,
            JsonEncode::OPTIONS => JSON_THROW_ON_ERROR,
        ]);

        // Keep the exact options for the follow-up verification request, and allow each challenge only once.
        $request->session()->put('webauthn.registration', [
            'options_json' => $optionsJson,
            'expires_at' => now()->addMinutes(5)->timestamp,
        ]);

        return response()->json(json_decode($optionsJson, true, 512, JSON_THROW_ON_ERROR));
    }

    public function credentials(Request $request): JsonResponse
    {
        $credentials = WebauthnCredential::query()
            ->where('usr_id', $request->user()->usr_id)
            ->orderByDesc('created_at')
            ->get(['id', 'device_name', 'created_at', 'last_used_at'])
            ->map(fn (WebauthnCredential $credential) => [
                'id' => (string) $credential->id,
                'device_name' => $credential->device_name ?: 'Passkey device',
                'created_at' => $credential->created_at?->toIso8601String(),
                'last_used_at' => $credential->last_used_at?->toIso8601String(),
            ]);

        return response()->json($credentials->values());
    }

    public function destroyCredential(Request $request, string $id): JsonResponse
    {
        $credential = WebauthnCredential::query()
            ->where('usr_id', $request->user()->usr_id)
            ->where('id', $id)
            ->firstOrFail();

        $credential->delete();

        return response()->json(['message' => 'Passkey removed from your account.']);
    }

    public function verify(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'credential' => ['required', 'array'],
            'device_name' => ['nullable', 'string', 'max:80'],
        ]);

        // Pulling the ceremony removes it from the session, so a challenge cannot be replayed.
        $ceremony = $request->session()->pull('webauthn.registration');
        if (! is_array($ceremony)
            || empty($ceremony['options_json'])
            || (int) ($ceremony['expires_at'] ?? 0) < now()->timestamp) {
            return response()->json([
                'message' => 'Passkey registration expired. Please start again.',
            ], 422);
        }

        try {
            $serializer = $this->serializer();
            $options = $serializer->deserialize(
                $ceremony['options_json'],
                PublicKeyCredentialCreationOptions::class,
                'json'
            );
            $credentialJson = json_encode($validated['credential'], JSON_THROW_ON_ERROR);
            $credential = $serializer->deserialize($credentialJson, PublicKeyCredential::class, 'json');

            if (! $credential->response instanceof AuthenticatorAttestationResponse) {
                return response()->json(['message' => 'Invalid passkey registration response.'], 422);
            }

            $rpId = (string) config('webauthn.rp_id');
            $origin = (string) config('webauthn.origin');
            $originHost = parse_url($origin, PHP_URL_HOST);
            abort_if($rpId === '' || ! is_string($originHost) || $originHost === '', 500, 'Passkey site settings are incomplete.');

            $steps = new CeremonyStepManagerFactory();
            // Exact origin checking plus the RP ID allowlist; the latter permits local HTTP development.
            $steps->setAllowedOrigins([$origin]);
            $steps->setSecuredRelyingPartyId([$rpId]);
            $record = AuthenticatorAttestationResponseValidator::create($steps->creationCeremony())
                ->check($credential->response, $options, $originHost);

            $credentialId = Base64UrlSafe::encodeUnpadded($record->publicKeyCredentialId);
            $recordJson = $serializer->serialize($record, 'json', [
                AbstractObjectNormalizer::SKIP_NULL_VALUES => true,
                JsonEncode::OPTIONS => JSON_THROW_ON_ERROR,
            ]);

            WebauthnCredential::query()->create([
                'id' => (string) Str::uuid(),
                'usr_id' => $request->user()->usr_id,
                'credential_id' => $credentialId,
                'public_key' => Base64UrlSafe::encodeUnpadded($record->credentialPublicKey),
                'sign_count' => $record->counter,
                'transports' => $record->transports,
                'device_name' => $validated['device_name'] ?: 'My passkey',
                'credential_record' => json_decode($recordJson, true, 512, JSON_THROW_ON_ERROR),
                'created_at' => now(),
            ]);

            return response()->json(['message' => 'Passkey registered successfully.']);
        } catch (\Illuminate\Database\QueryException $exception) {
            report($exception);
            if ($exception->getCode() === '23505') {
                // PostgreSQL unique_violation: this credential is already stored.
                return response()->json([
                    'message' => 'This passkey may already be registered. Please try another passkey.',
                ], 409);
            }

            return response()->json(['message' => 'Could not save this passkey. Please try again.'], 500);
        } catch (\Throwable $exception) {
            report($exception);
            return response()->json([
                'message' => 'Passkey verification failed. Please start registration again.',
            ], 422);
        }
    }
}
