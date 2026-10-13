<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0f172a">
    <title>داشبورد | سامانه تیکتینگ</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 font-sans text-slate-900 antialiased">
    <div class="min-h-screen lg:grid lg:grid-cols-[280px_1fr]">
        <aside class="hidden bg-slate-950 p-6 text-white lg:flex lg:flex-col">
            <div class="flex items-center gap-3 border-b border-white/10 pb-6">
                <div class="grid size-11 place-items-center rounded-2xl bg-teal-500/15 text-teal-300">
                    <svg class="size-6" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M7.5 8.5h9M7.5 12h6M9 19l-4 2v-4.5A7.5 7.5 0 0 1 4 13V8.5A4.5 4.5 0 0 1 8.5 4h7A4.5 4.5 0 0 1 20 8.5V13a4.5 4.5 0 0 1-4.5 4.5H12L9 19Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <div><p class="font-bold">سامانه تیکتینگ</p><p class="mt-0.5 text-xs text-slate-500">پنل مدیریت</p></div>
            </div>

            <nav class="mt-8" aria-label="منوی اصلی">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-2xl bg-white/10 px-4 py-3 text-sm font-bold text-white">
                    <svg class="size-5 text-teal-300" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 13h6V4H4v9Zm0 7h6v-4H4v4Zm10 0h6v-9h-6v9Zm0-12h6V4h-6v4Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>
                    داشبورد
                </a>
            </nav>

            <div class="mt-auto rounded-2xl border border-white/10 bg-white/[0.04] p-4">
                <p class="truncate text-sm font-bold">{{ auth()->user()->name }}</p>
                <p class="mt-1 truncate text-xs text-slate-500" dir="ltr">{{ auth()->user()->email }}</p>
                <form class="mt-4" method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl border border-white/10 px-3 py-2.5 text-xs font-bold text-slate-300 transition hover:border-rose-400/30 hover:bg-rose-400/10 hover:text-rose-300">خروج از حساب</button>
                </form>
            </div>
        </aside>

        <main class="min-w-0">
            <header class="flex h-20 items-center justify-between border-b border-slate-200 bg-white px-5 sm:px-8">
                <div><h1 class="text-lg font-black text-slate-950">داشبورد</h1><p class="mt-1 text-xs text-slate-500">نمای کلی فضای کاری</p></div>
                <div class="flex items-center gap-3">
                    <div class="hidden text-left sm:block"><p class="text-sm font-bold text-slate-800">{{ auth()->user()->name }}</p><p class="mt-0.5 text-xs text-slate-400" dir="ltr">{{ auth()->user()->email }}</p></div>
                    <div class="grid size-11 place-items-center rounded-full bg-teal-50 font-black text-teal-700 ring-4 ring-white shadow-sm">{{ mb_substr(auth()->user()->name, 0, 1) }}</div>
                    <form method="POST" action="{{ route('logout') }}" class="lg:hidden">
                        @csrf
                        <button type="submit" aria-label="خروج از حساب" class="grid size-10 place-items-center rounded-xl border border-slate-200 text-slate-500 transition hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600">
                            <svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M9 5H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h4m6-4 4-3-4-3m4 3H9" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                    </form>
                </div>
            </header>

            <section class="grid min-h-[calc(100vh-5rem)] place-items-center p-6 sm:p-10">
                <div class="w-full max-w-xl rounded-3xl border border-slate-200 bg-white p-8 text-center shadow-sm sm:p-12">
                    <div class="mx-auto grid size-20 place-items-center rounded-3xl bg-teal-50 text-teal-600 ring-8 ring-teal-50/50">
                        <svg class="size-9" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 13h6V4H4v9Zm0 7h6v-4H4v4Zm10 0h6v-9h-6v9Zm0-12h6V4h-6v4Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>
                    </div>
                    <h2 class="mt-7 text-2xl font-black text-slate-950">داشبورد آماده است</h2>
                    <p class="mx-auto mt-3 max-w-md leading-7 text-slate-500">فضای کاری شما با موفقیت ایجاد شده است. ابزارها و گزارش‌های سامانه در این بخش قرار خواهند گرفت.</p>
                    <div class="mt-8 inline-flex items-center gap-2 rounded-full bg-emerald-50 px-4 py-2 text-xs font-bold text-emerald-700"><span class="size-2 rounded-full bg-emerald-500"></span>ورود موفق و نشست فعال</div>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
