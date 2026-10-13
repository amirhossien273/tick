<?php

namespace App\Http\Controllers;

use App\Actions\GenerateSystemApiKey;
use App\Models\System;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class SystemApiKeyController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(
        Request $request,
        System $system,
        GenerateSystemApiKey $generateApiKey,
    ): RedirectResponse {
        Gate::authorize('update', $system);

        $system->issueApiKey($generateApiKey->handle())->save();

        activity('systems')
            ->causedBy($request->user())
            ->performedOn($system)
            ->event('api_key_rotated')
            ->log('API Key سامانه توسط مدیر تغییر کرد.');

        return back()
            ->with('success', 'API Key جدید صادر شد و کلید قبلی دیگر معتبر نیست.')
            ->with('api_key_revealed', true);
    }
}
