<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSystemStatusRequest;
use App\Models\System;
use Illuminate\Http\RedirectResponse;

class SystemStatusController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(UpdateSystemStatusRequest $request, System $system): RedirectResponse
    {
        $isActive = $request->boolean('is_active');

        $system->update(['is_active' => $isActive]);

        return back()->with(
            'success',
            $isActive
                ? 'دسترسی سامانه به API فعال شد.'
                : 'دسترسی سامانه به API غیرفعال شد.',
        );
    }
}
