<x-app-layout title="جزئیات کاربر" description="مشاهده وضعیت، اطلاعات و تاریخچه حساب {{ $user->name }}">
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-2 text-xs font-bold text-slate-400">
            <a href="{{ route('users.index') }}" class="transition hover:text-teal-700">مدیریت کاربران</a>
            <svg class="size-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m15 18-6-6 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
            <span class="text-slate-700">{{ $user->name }}</span>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('users.password.edit', $user) }}" class="inline-flex h-11 items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 text-xs font-black text-slate-600 shadow-sm transition hover:border-amber-200 hover:bg-amber-50 hover:text-amber-800">
                <svg class="size-4.5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M7 10V8a5 5 0 0 1 10 0v2M6 21h12a2 2 0 0 0 2-2v-7a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v7a2 2 0 0 0 2 2Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                بازنشانی رمز
            </a>
            <a href="{{ route('users.edit', $user) }}" class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-slate-950 px-4 text-xs font-black text-white shadow-lg transition hover:bg-teal-700">
                <svg class="size-4.5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m14 5 5 5M4 20l4.5-1 10-10a2.1 2.1 0 0 0-3-3l-10 10L4 20Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>
                ویرایش اطلاعات
            </a>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_380px]">
        <div class="flex flex-col gap-6">
            <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="relative overflow-hidden bg-slate-950 p-6 text-white sm:p-8">
                    <div class="pointer-events-none absolute -left-12 -top-16 size-56 rounded-full bg-teal-500/20 blur-3xl"></div>
                    <div class="relative flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-4">
                            <span class="grid size-16 shrink-0 place-items-center rounded-2xl border border-white/10 bg-white/10 text-2xl font-black text-teal-200">{{ mb_substr($user->name, 0, 1) }}</span>
                            <div class="min-w-0"><h2 class="truncate text-xl font-black">{{ $user->name }}</h2><p class="mt-2 truncate text-sm text-slate-400" dir="ltr">{{ $user->email }}</p></div>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <span @class(['inline-flex items-center gap-2 rounded-full px-3 py-1.5 text-xs font-bold', 'bg-emerald-400/10 text-emerald-300 ring-1 ring-inset ring-emerald-400/20' => $user->is_active, 'bg-rose-400/10 text-rose-300 ring-1 ring-inset ring-rose-400/20' => ! $user->is_active])><span @class(['size-2 rounded-full', 'bg-emerald-400' => $user->is_active, 'bg-rose-400' => ! $user->is_active])></span>{{ $user->is_active ? 'حساب فعال' : 'حساب غیرفعال' }}</span>
                            @if ($user->is_admin)<span class="rounded-full bg-violet-400/10 px-3 py-1.5 text-xs font-bold text-violet-300 ring-1 ring-inset ring-violet-400/20">مدیر سامانه</span>@endif
                        </div>
                    </div>
                </div>

                <dl class="grid gap-px bg-slate-100 sm:grid-cols-2">
                    <div class="bg-white p-5 sm:p-6"><dt class="text-xs font-bold text-slate-400">شناسه کاربر</dt><dd class="mt-2 truncate font-mono text-xs font-bold text-slate-700" dir="ltr">{{ $user->id }}</dd></div>
                    <div class="bg-white p-5 sm:p-6"><dt class="text-xs font-bold text-slate-400">تاریخ ایجاد حساب</dt><dd class="mt-2 text-sm font-black text-slate-800">{{ $user->created_at->format('Y/m/d - H:i') }}</dd></div>
                    <div class="bg-white p-5 sm:p-6"><dt class="text-xs font-bold text-slate-400">آخرین ورود</dt><dd class="mt-2 text-sm font-black text-slate-800">{{ $user->last_login_at?->format('Y/m/d - H:i') ?? 'هنوز وارد نشده است' }}</dd></div>
                    <div class="bg-white p-5 sm:p-6"><dt class="text-xs font-bold text-slate-400">آخرین ویرایش</dt><dd class="mt-2 text-sm font-black text-slate-800">{{ $user->updated_at->format('Y/m/d - H:i') }}</dd></div>
                </dl>
            </section>

            <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-7">
                <div class="flex items-center justify-between gap-4">
                    <div><h2 class="font-black text-slate-950">تاریخچه فعالیت</h2><p class="mt-2 text-xs text-slate-500">آخرین تغییرات ثبت‌شده برای این حساب</p></div>
                    <span class="grid size-10 place-items-center rounded-xl bg-slate-100 text-slate-600"><svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 7v5l3 2m6-2a9 9 0 1 1-3-6.7" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg></span>
                </div>

                <div class="mt-6 flex flex-col gap-1">
                    @forelse ($user->activities as $activity)
                        <div class="flex gap-4 rounded-2xl p-3 transition hover:bg-slate-50">
                            <div class="flex flex-col items-center"><span class="mt-1 size-2.5 rounded-full bg-teal-500 ring-4 ring-teal-50"></span><span class="mt-2 h-full w-px bg-slate-100"></span></div>
                            <div class="min-w-0 pb-4"><p class="text-sm font-bold text-slate-800">{{ match ($activity->event) { 'created' => 'حساب کاربری ایجاد شد.', 'updated' => 'اطلاعات حساب تغییر کرد.', 'password_reset' => 'رمز عبور توسط مدیر بازنشانی شد.', default => $activity->description } }}</p><p class="mt-1.5 text-xs text-slate-400">{{ $activity->created_at->format('Y/m/d - H:i') }}</p></div>
                        </div>
                    @empty
                        <div class="rounded-2xl bg-slate-50 p-5 text-center text-sm text-slate-500">فعالیتی برای این کاربر ثبت نشده است.</div>
                    @endforelse
                </div>
            </section>
        </div>

        <aside class="flex flex-col gap-5">
            <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="font-black text-slate-950">کنترل دسترسی</h2>
                <p class="mt-2 text-sm leading-7 text-slate-500">با غیرفعال‌سازی، کاربر دیگر امکان ورود به سامانه را نخواهد داشت.</p>
                <form method="POST" action="{{ route('users.status.update', $user) }}" class="mt-5" data-confirm="{{ $user->is_active ? 'آیا از غیرفعال‌کردن این کاربر مطمئن هستید؟' : 'این حساب دوباره فعال شود؟' }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="is_active" value="{{ $user->is_active ? 0 : 1 }}">
                    <button type="submit" @disabled(auth()->user()->is($user) && $user->is_active) @class(['inline-flex h-12 w-full items-center justify-center gap-2 rounded-2xl text-sm font-black transition disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400', 'bg-rose-50 text-rose-700 hover:bg-rose-100' => $user->is_active, 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' => ! $user->is_active])>
                        {{ $user->is_active ? 'غیرفعال‌کردن حساب' : 'فعال‌کردن حساب' }}
                    </button>
                </form>
                @if (auth()->user()->is($user))<p class="mt-3 text-center text-[11px] leading-5 text-slate-400">امکان غیرفعال‌کردن حساب فعلی وجود ندارد.</p>@endif
            </section>

            <section class="rounded-3xl border border-amber-100 bg-amber-50 p-6">
                <div class="flex items-start gap-3">
                    <span class="grid size-10 shrink-0 place-items-center rounded-xl bg-amber-100 text-amber-700"><svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 8v4m0 4h.01M10.3 4.45 3.42 16.2A2 2 0 0 0 5.15 19h13.7a2 2 0 0 0 1.73-2.8L13.7 4.45a2 2 0 0 0-3.4 0Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg></span>
                    <div><h3 class="text-sm font-black text-amber-950">بازنشانی امن رمز</h3><p class="mt-2 text-xs leading-6 text-amber-800/80">با تغییر رمز، نشست‌های قبلی کاربر از دیتابیس حذف می‌شوند و ورود بعدی فقط با رمز جدید ممکن است.</p></div>
                </div>
            </section>
        </aside>
    </div>
</x-app-layout>
