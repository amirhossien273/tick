<x-app-layout title="بازنشانی رمز عبور" description="تعیین رمز عبور جدید برای {{ $user->name }}">
    <div class="mx-auto max-w-3xl">
        <div class="mb-6 flex items-center gap-2 text-xs font-bold text-slate-400">
            <a href="{{ route('users.index') }}" class="transition hover:text-teal-700">مدیریت کاربران</a>
            <svg class="size-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m15 18-6-6 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
            <a href="{{ route('users.show', $user) }}" class="transition hover:text-teal-700">{{ $user->name }}</a>
            <svg class="size-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m15 18-6-6 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
            <span class="text-slate-700">رمز عبور</span>
        </div>

        <form method="POST" action="{{ route('users.password.update', $user) }}" class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            @csrf
            @method('PATCH')
            <div class="border-b border-slate-100 p-6 sm:p-8">
                <div class="flex items-center gap-4">
                    <span class="grid size-12 shrink-0 place-items-center rounded-2xl bg-amber-50 text-amber-700">
                        <svg class="size-6" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M7 10V8a5 5 0 0 1 10 0v2M6 21h12a2 2 0 0 0 2-2v-7a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v7a2 2 0 0 0 2 2Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                    </span>
                    <div>
                        <h2 class="font-black text-slate-950">رمز جدید حساب</h2>
                        <p class="mt-1 text-sm text-slate-500" dir="ltr">{{ $user->email }}</p>
                    </div>
                </div>
            </div>

            <div class="grid gap-5 p-6 sm:grid-cols-2 sm:p-8">
                <div>
                    <label for="password" class="mb-2 block text-sm font-bold text-slate-700">رمز عبور جدید</label>
                    <div class="relative">
                        <input id="password" name="password" type="password" dir="ltr" autocomplete="new-password" required autofocus class="h-13 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 pl-12 text-left text-sm outline-none transition focus:border-teal-500 focus:bg-white focus:ring-4 focus:ring-teal-500/10" placeholder="حداقل ۸ کاراکتر">
                        <button type="button" class="absolute inset-y-0 left-0 grid w-12 place-items-center text-slate-400 hover:text-slate-700" aria-label="نمایش رمز عبور" aria-pressed="false" data-password-toggle data-password-target="password"><svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z" stroke="currentColor" stroke-width="1.7"/><circle cx="12" cy="12" r="2.5" stroke="currentColor" stroke-width="1.7"/></svg></button>
                    </div>
                    @error('password')<p class="mt-2 text-xs font-bold text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="password_confirmation" class="mb-2 block text-sm font-bold text-slate-700">تکرار رمز عبور جدید</label>
                    <div class="relative">
                        <input id="password_confirmation" name="password_confirmation" type="password" dir="ltr" autocomplete="new-password" required class="h-13 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 pl-12 text-left text-sm outline-none transition focus:border-teal-500 focus:bg-white focus:ring-4 focus:ring-teal-500/10" placeholder="تکرار رمز عبور">
                        <button type="button" class="absolute inset-y-0 left-0 grid w-12 place-items-center text-slate-400 hover:text-slate-700" aria-label="نمایش تکرار رمز عبور" aria-pressed="false" data-password-toggle data-password-target="password_confirmation"><svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z" stroke="currentColor" stroke-width="1.7"/><circle cx="12" cy="12" r="2.5" stroke="currentColor" stroke-width="1.7"/></svg></button>
                    </div>
                </div>
            </div>

            <div class="flex flex-col-reverse gap-3 border-t border-slate-100 bg-slate-50/70 p-6 sm:flex-row sm:justify-end sm:p-8">
                <a href="{{ route('users.show', $user) }}" class="inline-flex h-12 items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 text-sm font-bold text-slate-600 transition hover:border-slate-300">انصراف</a>
                <button type="submit" class="inline-flex h-12 items-center justify-center gap-2 rounded-2xl bg-slate-950 px-6 text-sm font-black text-white shadow-lg transition hover:-translate-y-0.5 hover:bg-teal-700">ذخیره رمز جدید</button>
            </div>
        </form>
    </div>
</x-app-layout>
