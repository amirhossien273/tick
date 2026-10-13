<?php

namespace App\Http\Middleware;

use App\Models\System;
use Closure;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateSystemApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->bearerToken() ?: $request->header('X-API-Key');

        if (! is_string($apiKey) || $apiKey === '' || mb_strlen($apiKey) > 255) {
            return response()->json(['message' => 'کلید API معتبر نیست.'], Response::HTTP_UNAUTHORIZED);
        }

        $system = System::query()
            ->where('api_key_hash', System::hashApiKey($apiKey))
            ->where('is_active', true)
            ->first();

        try {
            $hasValidKey = $system !== null && hash_equals($system->api_key, $apiKey);
        } catch (DecryptException) {
            $hasValidKey = false;
        }

        if (! $hasValidKey) {
            return response()->json(['message' => 'کلید API معتبر نیست.'], Response::HTTP_UNAUTHORIZED);
        }

        $system->forceFill(['last_used_at' => now()])->saveQuietly();
        $request->attributes->set('authenticatedSystem', $system);

        return $next($request);
    }
}
