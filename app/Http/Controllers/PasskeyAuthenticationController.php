<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WebauthnCredential;
use App\Services\AuditActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use ParagonIE\ConstantTime\Base64UrlSafe;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Webauthn\AttestationStatement\AttestationStatementSupportManager;
use Webauthn\AuthenticatorAssertionResponse;
use Webauthn\AuthenticatorAssertionResponseValidator;
use Webauthn\CeremonyStep\CeremonyStepManagerFactory;
use Webauthn\CredentialRecord;
use Webauthn\Denormalizer\WebauthnSerializerFactory;
use Webauthn\PublicKeyCredential;
use Webauthn\PublicKeyCredentialRequestOptions;
use Webauthn\Counter\CounterChecker;

class PasskeyAuthenticationController extends Controller
{
    private function serializer(): \Symfony\Component\Serializer\SerializerInterface
    {
        return (new WebauthnSerializerFactory(
            AttestationStatementSupportManager::create()
        ))->create();
    }

    public function options(Request $request): JsonResponse
    {
        $rpId = (string) config('webauthn.rp_id');
        abort_if($rpId === '', 500, 'Passkey site settings are incomplete.');

        // Discoverable credentials let the authenticator identify the account without asking for email first.
        $options = PublicKeyCredentialRequestOptions::create(
            random_bytes(32),
            $rpId,
            [],
            PublicKeyCredentialRequestOptions::USER_VERIFICATION_REQUIREMENT_REQUIRED,
            60000
        );

        $optionsJson = $this->serializer()->serialize($options, 'json', [
            AbstractObjectNormalizer::SKIP_NULL_VALUES => true,
            JsonEncode::OPTIONS => JSON_THROW_ON_ERROR,
        ]);

        $request->session()->put('webauthn.authentication', [
            'options_json' => $optionsJson,
            'expires_at' => now()->addMinutes(5)->timestamp,
        ]);

        return response()->json(json_decode($optionsJson, true, 512, JSON_THROW_ON_ERROR));
    }

    public function verify(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'credential' => ['required', 'array'],
        ]);

        $ceremony = $request->session()->pull('webauthn.authentication');
        if (! is_array($ceremony)
            || empty($ceremony['options_json'])
            || (int) ($ceremony['expires_at'] ?? 0) < now()->timestamp) {
            return response()->json(['message' => 'Passkey sign-in expired. Please try again.'], 422);
        }

        try {
            $serializer = $this->serializer();
            $options = $serializer->deserialize(
                $ceremony['options_json'],
                PublicKeyCredentialRequestOptions::class,
                'json'
            );
            $credentialJson = json_encode($validated['credential'], JSON_THROW_ON_ERROR);
            $credential = $serializer->deserialize($credentialJson, PublicKeyCredential::class, 'json');

            if (! $credential->response instanceof AuthenticatorAssertionResponse) {
                return response()->json(['message' => 'Invalid passkey sign-in response.'], 422);
            }

            $credentialId = Base64UrlSafe::encodeUnpadded($credential->rawId);
            $storedCredential = WebauthnCredential::query()
                ->where('credential_id', $credentialId)
                ->first();

            if (! $storedCredential || ! is_array($storedCredential->credential_record)) {
                return response()->json(['message' => 'Passkey not recognized. Sign in with your password or register this device first.'], 401);
            }

            $recordJson = json_encode($storedCredential->credential_record, JSON_THROW_ON_ERROR);
            $record = $serializer->deserialize($recordJson, CredentialRecord::class, 'json');
            $rpId = (string) config('webauthn.rp_id');
            $origin = (string) config('webauthn.origin');
            $originHost = parse_url($origin, PHP_URL_HOST);
            abort_if($rpId === '' || ! is_string($originHost) || $originHost === '', 500, 'Passkey site settings are incomplete.');

            $steps = new CeremonyStepManagerFactory();
            $steps->setAllowedOrigins([$origin]);
            $steps->setSecuredRelyingPartyId([$rpId]);
            // Some synced/platform passkeys report a zero counter by design. Compare counters when supported.
            $steps->setCounterChecker(new class implements CounterChecker
            {
                public function check(CredentialRecord $credentialRecord, int $currentCounter): void
                {
                    if ($credentialRecord->counter !== 0 && $currentCounter !== 0
                        && $currentCounter <= $credentialRecord->counter) {
                        throw \Webauthn\Exception\CounterException::create(
                            $currentCounter,
                            $credentialRecord->counter,
                            'Invalid authenticator counter.'
                        );
                    }
                }
            });
            $updatedRecord = AuthenticatorAssertionResponseValidator::create($steps->requestCeremony())
                ->check(
                    $record,
                    $credential->response,
                    $options,
                    $originHost,
                    $credential->response->userHandle
                );

            $user = User::query()->find($storedCredential->usr_id);
            if (! $user || ! $user->usr_is_active) {
                return response()->json(['message' => 'This account is inactive. Please contact the administrator.'], 403);
            }

            if ($user->usr_locked_until && now()->lt($user->usr_locked_until)) {
                return response()->json(['message' => 'This account is temporarily locked. Please sign in later or contact the administrator.'], 423);
            }

            if ($user->usr_failed_login_attempts > 0 || $user->usr_locked_until) {
                $user->usr_failed_login_attempts = 0;
                $user->usr_locked_until = null;
                $user->save();
            }

            $storedCredential->forceFill([
                'credential_record' => json_decode($serializer->serialize($updatedRecord, 'json', [
                    AbstractObjectNormalizer::SKIP_NULL_VALUES => true,
                    JsonEncode::OPTIONS => JSON_THROW_ON_ERROR,
                ]), true, 512, JSON_THROW_ON_ERROR),
                'sign_count' => $updatedRecord->counter,
                'last_used_at' => now(),
            ])->save();

            $redirect = $this->redirectForRole($user->usr_role);
            if ($redirect === null) {
                return response()->json(['message' => 'This account role cannot sign in.'], 403);
            }

            Auth::login($user);
            $request->session()->regenerate();
            AuditActivityLogger::record(
                $user,
                'Logged in with passkey',
                'webauthn_credentials',
                'Successful passkey sign-in.',
                $request->ip(),
                (string) $storedCredential->id
            );

            return response()->json([
                'message' => 'Passkey sign-in successful.',
                'redirect' => $redirect,
            ]);
        } catch (\Throwable $exception) {
            // Do not log the assertion payload; malformed WebAuthn responses can contain untrusted input.
            return response()->json(['message' => 'Passkey verification failed. Please try again or use your password.'], 422);
        }
    }

    private function redirectForRole(?string $role): ?string
    {
        return match ($role) {
            'system_admin' => route('admin.dashboard'),
            'department_chair' => route('chair.dashboard'),
            'dean' => route('dean.dashboard'),
            'faculty' => route('faculty.dashboard'),
            default => null,
        };
    }
}
