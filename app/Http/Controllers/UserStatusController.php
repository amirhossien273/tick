<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserStatusRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

class UserStatusController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(UpdateUserStatusRequest $request, User $user): RedirectResponse
    {
        $isActive = $request->boolean('is_active');

        $user->update(['is_active' => $isActive]);

        return back()->with(
            'success',
            $isActive ? 'حساب کاربر فعال شد.' : 'حساب کاربر غیرفعال شد و دیگر امکان ورود ندارد.',
        );
    }
}
