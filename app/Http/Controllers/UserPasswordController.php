<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResetUserPasswordRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class UserPasswordController extends Controller
{
    public function edit(User $user): View
    {
        Gate::authorize('update', $user);

        return view('users.password', ['user' => $user]);
    }

    public function update(ResetUserPasswordRequest $request, User $user): RedirectResponse
    {
        $user->forceFill([
            'password' => $request->validated('password'),
            'remember_token' => null,
        ])->save();

        if (config('session.driver') === 'database') {
            DB::table((string) config('session.table', 'sessions'))
                ->where('user_id', $user->getKey())
                ->delete();
        }

        activity('users')
            ->causedBy($request->user())
            ->performedOn($user)
            ->event('password_reset')
            ->log('رمز عبور کاربر توسط مدیر بازنشانی شد.');

        return redirect()
            ->route('users.show', $user)
            ->with('success', 'رمز عبور کاربر با موفقیت بازنشانی شد.');
    }
}
