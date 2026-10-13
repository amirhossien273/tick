@props([
    'title',
    'description' => null,
])

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0f172a">
    <title>{{ $title }} | سامانه تیکتینگ</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 font-sans text-slate-900 antialiased">
    <div class="min-h-screen lg:grid lg:grid-cols-[288px_minmax(0,1fr)]">
        <div class="fixed inset-0 z-40 hidden bg-slate-950/60 backdrop-blur-sm lg:hidden" data-sidebar-overlay></div>

        <aside class="fixed inset-y-0 right-0 z-50 flex w-72 translate-x-full flex-col overflow-hidden bg-slate-950 p-5 text-white shadow-2xl transition-transform duration-300 lg:static lg:w-auto lg:translate-x-0 lg:shadow-none" data-sidebar>
            <div class="pointer-events-none absolute -right-24 -top-24 size-72 rounded-full bg-teal-500/10 blur-3xl"></div>

            <div class="relative flex items-center justify-between border-b border-white/10 pb-5">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                    <span class="grid size-11 place-items-center rounded-2xl bg-teal-500/15 text-teal-300 ring-1 ring-inset ring-teal-400/20">
                        <svg class="size-6" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M7.5 8.5h9M7.5 12h6M9 19l-4 2v-4.5A7.5 7.5 0 0 1 4 13V8.5A4.5 4.5 0 0 1 8.5 4h7A4.5 4.5 0 0 1 20 8.5V13a4.5 4.5 0 0 1-4.5 4.5H12L9 19Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                    <span>
                        <span class="block text-sm font-black">سامانه تیکتینگ</span>
                        <span class="mt-1 block text-[11px] text-slate-500">مرکز یکپارچه پشتیبانی</span>
                    </span>
                </a>
                <button type="button" class="grid size-9 place-items-center rounded-xl text-slate-400 transition hover:bg-white/10 hover:text-white lg:hidden" aria-label="بستن منو" data-sidebar-close>
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                </button>
            </div>

            <nav class="relative mt-7 flex flex-col gap-2" aria-label="منوی اصلی">
                <p class="mb-2 px-3 text-[10px] font-bold tracking-[0.18em] text-slate-600">منوی اصلی</p>
                <a href="{{ route('dashboard') }}" @class([
                    'group flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-bold transition',
                    'bg-teal-500/15 text-teal-200 ring-1 ring-inset ring-teal-400/20' => request()->routeIs('dashboard'),
                    'text-slate-400 hover:bg-white/[0.06] hover:text-white' => ! request()->routeIs('dashboard'),
                ])>
                    <svg class="size-5 transition group-hover:text-teal-300" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 13h6V4H4v9Zm0 7h6v-4H4v4Zm10 0h6v-9h-6v9Zm0-12h6V4h-6v4Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>
                    داشبورد
                </a>

                @can('viewAny', App\Models\User::class)
                    <a href="{{ route('users.index') }}" @class([
                        'group flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-bold transition',
                        'bg-teal-500/15 text-teal-200 ring-1 ring-inset ring-teal-400/20' => request()->routeIs('users.*'),
                        'text-slate-400 hover:bg-white/[0.06] hover:text-white' => ! request()->routeIs('users.*'),
                    ])>
                        <svg class="size-5 transition group-hover:text-teal-300" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M16 20v-1.5a3.5 3.5 0 0 0-3.5-3.5h-5A3.5 3.5 0 0 0 4 18.5V20m6-9a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Zm7-2v6m3-3h-6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        مدیریت کاربران
                    </a>
                @endcan

                @can('viewAny', App\Models\System::class)
                    <a href="{{ route('systems.index') }}" @class([
                        'group flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-bold transition',
                        'bg-teal-500/15 text-teal-200 ring-1 ring-inset ring-teal-400/20' => request()->routeIs('systems.*'),
                        'text-slate-400 hover:bg-white/[0.06] hover:text-white' => ! request()->routeIs('systems.*'),
                    ])>
                        <svg class="size-5 transition group-hover:text-teal-300" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 4h14a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Zm0 9h14a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2Zm2-5h.01M7 17h.01" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                        مدیریت سامانه‌ها
                    </a>
                @endcan
            </nav>

            <div class="relative mt-auto rounded-2xl border border-white/10 bg-white/[0.04] p-4">
                <div class="flex items-center gap-3">
                    <span class="grid size-10 shrink-0 place-items-center rounded-xl bg-teal-400/10 text-sm font-black text-teal-300">{{ mb_substr(auth()->user()->name, 0, 1) }}</span>
                    <span class="min-w-0">
                        <span class="block truncate text-sm font-bold">{{ auth()->user()->name }}</span>
                        <span class="mt-1 block truncate text-[11px] text-slate-500" dir="ltr">{{ auth()->user()->email }}</span>
                    </span>
                </div>
                <form class="mt-4" method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl border border-white/10 px-3 py-2.5 text-xs font-bold text-slate-300 transition hover:border-rose-400/30 hover:bg-rose-400/10 hover:text-rose-300">
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M9 5H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h4m6-4 4-3-4-3m4 3H9" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        خروج از حساب
                    </button>
                </form>
            </div>
        </aside>

        <div class="min-w-0">
            <header class="sticky top-0 z-30 border-b border-slate-200/80 bg-white/90 px-4 py-4 backdrop-blur-xl sm:px-7 lg:px-10">
                <div class="mx-auto flex max-w-[1600px] items-center justify-between gap-4">
                    <div class="flex min-w-0 items-center gap-3">
                        <button type="button" class="grid size-10 shrink-0 place-items-center rounded-xl border border-slate-200 bg-white text-slate-600 shadow-sm transition hover:border-teal-200 hover:text-teal-700 lg:hidden" aria-label="باز کردن منو" data-sidebar-open>
                            <svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                        </button>
                        <div class="min-w-0">
                            <h1 class="truncate text-lg font-black text-slate-950 sm:text-xl">{{ $title }}</h1>
                            @if ($description)
                                <p class="mt-1 hidden truncate text-xs text-slate-500 sm:block">{{ $description }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="hidden text-left sm:block">
                            <p class="text-xs font-bold text-slate-700">{{ auth()->user()->name }}</p>
                            <p class="mt-1 text-[10px] text-slate-400">{{ auth()->user()->is_admin ? 'مدیر سامانه' : 'کاربر سامانه' }}</p>
                        </div>
                        <span class="grid size-10 place-items-center rounded-xl bg-slate-950 text-sm font-black text-white shadow-lg shadow-slate-950/10">{{ mb_substr(auth()->user()->name, 0, 1) }}</span>
                    </div>
                </div>
            </header>

            <main class="mx-auto max-w-[1600px] p-4 sm:p-7 lg:p-10">
                @if (session('success'))
                    <div class="mb-6 flex items-center justify-between gap-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3.5 text-sm text-emerald-800 shadow-sm" role="status" data-alert>
                        <div class="flex items-center gap-3">
                            <span class="grid size-8 place-items-center rounded-xl bg-emerald-100 text-emerald-700">
                                <svg class="size-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m5 13 4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </span>
                            <span class="font-bold">{{ session('success') }}</span>
                        </div>
                        <button type="button" class="grid size-8 place-items-center rounded-lg text-emerald-600 transition hover:bg-emerald-100" aria-label="بستن پیام" data-alert-close>
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m7 7 10 10M17 7 7 17" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                        </button>
                    </div>
                @endif

                @if ($errors->has('is_active'))
                    <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3.5 text-sm font-bold text-rose-700" role="alert">{{ $errors->first('is_active') }}</div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
