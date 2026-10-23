<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CaptchaChallenge
{
    public static function siteKey(): ?string
    {
        return config('services.recaptcha.site_key');
    }

    public static function verify(Request $request): bool
    {
        $secret = config('services.recaptcha.secret_key');
        $token = $request->input('g-recaptcha-response');

        if (!$secret || !is_string($token) || $token === '') {
            return false;
        }

        try {
            $response = Http::asForm()->timeout(5)->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $secret,
                'response' => $token,
                'remoteip' => $request->ip(),
            ]);

            return $response->successful() && $response->json('success') === true;
        } catch (\Throwable) {
            return false;
        }
    }
}
