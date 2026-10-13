@php
    $isEditing = isset($user);
@endphp

<div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_300px]">
    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
        <div class="mb-7 flex items-start gap-4 border-b border-slate-100 pb-6">
            <span class="grid size-12 shrink-0 place-items-center rounded-2xl bg-teal-50 text-teal-700">
                <svg class="size-6" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M16 20v-1.5a3.5 3.5 0 0 0-3.5-3.5h-5A3.5 3.5 0 0 0 4 18.5V20m6-9a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
            </span>
            <div>
                <h2 class="font-black text-slate-950">اطلاعات حساب کاربری</h2>
                <p class="mt-2 text-sm leading-6 text-slate-500">نام و ایمیل برای شناسایی کاربر در سامانه استفاده می‌شود.</p>
            </div>
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="name" class="mb-2 block text-sm font-bold text-slate-700">نام و نام خانوادگی <span class="text-rose-500">*</span></label>
                <input id="name" name="name" type="text" value="{{ old('name', $user->name ?? '') }}" autocomplete="name" required
                       class="h-13 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none transition placeholder:text-slate-400 hover:border-slate-300 focus:border-teal-500 focus:bg-white focus:ring-4 focus:ring-teal-500/10"
                       placeholder="مثلاً علی رضایی">
                @error('name')<p class="mt-2 text-xs font-bold text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="email" class="mb-2 block text-sm font-bold text-slate-700">ایمیل <span class="text-rose-500">*</span></label>
                <input id="email" name="email" type="email" value="{{ old('email', $user->email ?? '') }}" dir="ltr" autocomplete="email" required
                       class="h-13 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-left text-sm outline-none transition placeholder:text-slate-400 hover:border-slate-300 focus:border-teal-500 focus:bg-white focus:ring-4 focus:ring-teal-500/10"
                       placeholder="user@example.com">
                @error('email')<p class="mt-2 text-xs font-bold text-rose-600">{{ $message }}</p>@enderror
            </div>
        </div>

        @unless ($isEditing)
            <div class="mt-7 border-t border-slate-100 pt-7">
                <h3 class="mb-5 text-sm font-black text-slate-900">رمز عبور اولیه</h3>
                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="password" class="mb-2 block text-sm font-bold text-slate-700">رمز عبور <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <input id="password" name="password" type="password" dir="ltr" autocomplete="new-password" required
                                   class="h-13 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 pl-12 text-left text-sm outline-none transition placeholder:text-slate-400 hover:border-slate-300 focus:border-teal-500 focus:bg-white focus:ring-4 focus:ring-teal-500/10"
                                   placeholder="حداقل ۸ کاراکتر">
                            <button type="button" class="absolute inset-y-0 left-0 grid w-12 place-items-center text-slate-400 transition hover:text-slate-700" aria-label="نمایش رمز عبور" aria-pressed="false" data-password-toggle data-password-target="password">
                                <svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z" stroke="currentColor" stroke-width="1.7"/><circle cx="12" cy="12" r="2.5" stroke="currentColor" stroke-width="1.7"/></svg>
                            </button>
                        </div>
                        @error('password')<p class="mt-2 text-xs font-bold text-rose-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="mb-2 block text-sm font-bold text-slate-700">تکرار رمز عبور <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <input id="password_confirmation" name="password_confirmation" type="password" dir="ltr" autocomplete="new-password" required
                                   class="h-13 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 pl-12 text-left text-sm outline-none transition placeholder:text-slate-400 hover:border-slate-300 focus:border-teal-500 focus:bg-white focus:ring-4 focus:ring-teal-500/10"
                                   placeholder="تکرار رمز عبور">
                            <button type="button" class="absolute inset-y-0 left-0 grid w-12 place-items-center text-slate-400 transition hover:text-slate-700" aria-label="نمایش تکرار رمز عبور" aria-pressed="false" data-password-toggle data-password-target="password_confirmation">
                                <svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z" stroke="currentColor" stroke-width="1.7"/><circle cx="12" cy="12" r="2.5" stroke="currentColor" stroke-width="1.7"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endunless
    </div>

    <aside class="flex flex-col gap-5">
        @unless ($isEditing)
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-black text-slate-900">وضعیت اولیه حساب</p>
                <label class="mt-4 flex cursor-pointer items-center justify-between gap-4 rounded-2xl border border-slate-200 bg-slate-50 p-4 transition hover:border-teal-200">
                    <span>
                        <span class="block text-sm font-bold text-slate-800">کاربر فعال باشد</span>
                        <span class="mt-1 block text-xs leading-5 text-slate-500">اجازه ورود بلافاصله پس از ساخت</span>
                    </span>
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true)) class="size-5 rounded border-slate-300 accent-teal-600 focus:ring-teal-500">
                </label>
            </div>
        @endunless

        <div class="rounded-3xl border border-blue-100 bg-blue-50 p-5">
            <div class="flex items-start gap-3">
                <span class="grid size-9 shrink-0 place-items-center rounded-xl bg-blue-100 text-blue-700">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 8h.01M11 12h1v4h1m8-4a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                </span>
                <div>
                    <p class="text-sm font-black text-blue-950">نکته امنیتی</p>
                    <p class="mt-2 text-xs leading-6 text-blue-800/80">اطلاعات ورود را فقط از مسیر امن در اختیار کاربر قرار دهید. رمز عبور در دیتابیس به‌صورت هش‌شده ذخیره می‌شود.</p>
                </div>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="inline-flex h-12 flex-1 items-center justify-center gap-2 rounded-2xl bg-slate-950 px-5 text-sm font-black text-white shadow-lg shadow-slate-950/15 transition hover:-translate-y-0.5 hover:bg-teal-700 focus:outline-none focus-visible:ring-4 focus-visible:ring-teal-500/20">
                {{ $submitLabel }}
            </button>
            <a href="{{ $isEditing ? route('users.show', $user) : route('users.index') }}" class="inline-flex h-12 items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 text-sm font-bold text-slate-600 transition hover:border-slate-300 hover:text-slate-900">انصراف</a>
        </div>
    </aside>
</div>
