<?php

namespace App\Http\Middleware;

use App\Models\System;
use Closure;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SourceSystemApiKeyMiddleware
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
            return $this->unauthorizedResponse();
        }

        $sourceSystem = System::query()
            ->where('api_key_hash', System::hashApiKey($apiKey))
            ->where('is_active', true)
            ->first();

        try {
            $hasValidKey = $sourceSystem !== null && hash_equals($sourceSystem->api_key, $apiKey);
        } catch (DecryptException) {
            $hasValidKey = false;
        }

        if (! $hasValidKey) {
            return $this->unauthorizedResponse();
        }

        $sourceSystem->forceFill(['last_used_at' => now()])->saveQuietly();
        $request->attributes->set('sourceSystem', $sourceSystem);
        $request->attributes->set('authenticatedSystem', $sourceSystem);

        return $next($request);
    }

    private function unauthorizedResponse(): Response
    {
        return response()->json([
            'message' => 'کلید API معتبر نیست.',
        ], Response::HTTP_UNAUTHORIZED);
    }
}
