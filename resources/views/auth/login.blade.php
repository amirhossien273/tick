<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0f172a">
    <title>ورود به سامانه تیکتینگ</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 font-sans text-slate-900 antialiased">
    <main class="relative min-h-screen overflow-hidden bg-slate-50 lg:grid lg:grid-cols-[minmax(480px,0.92fr)_minmax(560px,1.08fr)]">
        <section class="relative z-10 flex min-h-screen items-center justify-center bg-white px-6 py-12 sm:px-10 lg:px-16 xl:px-24">
            <div class="w-full max-w-md">
                <div class="mb-10 flex items-center gap-3 lg:hidden">
                    <div class="grid size-11 place-items-center rounded-2xl bg-teal-600 text-white shadow-lg shadow-teal-600/20">
                        <svg class="size-6" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M7.5 8.5h9M7.5 12h6M9 19l-4 2v-4.5A7.5 7.5 0 0 1 4 13V8.5A4.5 4.5 0 0 1 8.5 4h7A4.5 4.5 0 0 1 20 8.5V13a4.5 4.5 0 0 1-4.5 4.5H12L9 19Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold text-slate-900">سامانه تیکتینگ</p>
                        <p class="text-xs text-slate-500">مرکز یکپارچه پشتیبانی</p>
                    </div>
                </div>

                <div class="mb-8">
                    <div class="mb-5 inline-flex items-center gap-2 rounded-full border border-teal-100 bg-teal-50 px-3 py-1.5 text-xs font-bold text-teal-700">
                        <span class="size-1.5 rounded-full bg-teal-500"></span>
                        پنل مدیریت
                    </div>
                    <h1 class="text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">خوش آمدید</h1>
                    <p class="mt-3 leading-7 text-slate-500">برای دسترسی به میز کار پشتیبانی، اطلاعات حساب خود را وارد کنید.</p>
                </div>

                @if ($errors->any())
                    <div class="mb-6 flex items-start gap-3 rounded-2xl border border-rose-100 bg-rose-50 px-4 py-3.5 text-sm text-rose-700" role="alert">
                        <svg class="mt-0.5 size-5 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M12 8v4m0 4h.01M10.3 4.45 3.42 16.2A2 2 0 0 0 5.15 19h13.7a2 2 0 0 0 1.73-2.8L13.7 4.45a2 2 0 0 0-3.4 0Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                        <div>
                            <p class="font-bold">ورود انجام نشد</p>
                            <p class="mt-1 leading-6">{{ $errors->first() }}</p>
                        </div>
                    </div>
                @endif

                <form class="space-y-5" method="POST" action="{{ route('login.store') }}" data-login-form>
                    @csrf

                    <div>
                        <label for="email" class="mb-2 block text-sm font-bold text-slate-700">ایمیل</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 right-0 grid w-12 place-items-center text-slate-400">
                                <svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="m4 6 8 6 8-6M5 19h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" dir="ltr" autocomplete="email" autofocus required
                                   class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 pr-12 pl-4 text-left text-sm outline-none transition placeholder:text-slate-400 hover:border-slate-300 focus:border-teal-500 focus:bg-white focus:ring-4 focus:ring-teal-500/10"
                                   placeholder="name@example.com">
                        </div>
                        @error('email')
                            <p class="mt-2 text-xs font-medium text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="mb-2 block text-sm font-bold text-slate-700">رمز عبور</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 right-0 grid w-12 place-items-center text-slate-400">
                                <svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M7 10V8a5 5 0 0 1 10 0v2M6 21h12a2 2 0 0 0 2-2v-7a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v7a2 2 0 0 0 2 2Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                                </svg>
                            </span>
                            <input id="password" name="password" type="password" dir="ltr" autocomplete="current-password" required
                                   class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 pr-12 pl-12 text-left text-sm outline-none transition placeholder:text-slate-400 hover:border-slate-300 focus:border-teal-500 focus:bg-white focus:ring-4 focus:ring-teal-500/10"
                                   placeholder="رمز عبور خود را وارد کنید">
                            <button type="button" class="absolute inset-y-0 left-0 grid w-12 place-items-center rounded-l-2xl text-slate-400 transition hover:text-slate-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-teal-500" aria-label="نمایش رمز عبور" aria-pressed="false" data-password-toggle>
                                <svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                    <circle cx="12" cy="12" r="2.5" stroke="currentColor" stroke-width="1.7"/>
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-2 text-xs font-medium text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between gap-4">
                        <label class="inline-flex cursor-pointer items-center gap-2.5 text-sm text-slate-600">
                            <input type="hidden" name="remember" value="0">
                            <input type="checkbox" name="remember" value="1" @checked(old('remember')) class="size-4.5 rounded border-slate-300 text-teal-600 accent-teal-600 focus:ring-teal-500">
                            مرا به خاطر بسپار
                        </label>
                        <span class="text-xs text-slate-400">ورود امن و محافظت‌شده</span>
                    </div>

                    <button type="submit" class="group flex h-14 w-full items-center justify-center gap-2 rounded-2xl bg-slate-950 px-5 text-sm font-bold text-white shadow-xl shadow-slate-950/15 transition duration-200 hover:-translate-y-0.5 hover:bg-teal-700 hover:shadow-teal-800/20 focus:outline-none focus-visible:ring-4 focus-visible:ring-teal-500/30 disabled:cursor-wait disabled:opacity-70" data-submit-button>
                        <span data-submit-label>ورود به داشبورد</span>
                        <svg class="size-5 transition-transform group-hover:-translate-x-1" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="m15 18-6-6 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </form>

                <p class="mt-10 text-center text-xs leading-6 text-slate-400">دسترسی به این بخش فقط برای کاربران مجاز امکان‌پذیر است.</p>
            </div>
        </section>

        <aside class="relative hidden min-h-screen overflow-hidden bg-slate-950 p-12 text-white lg:flex lg:flex-col lg:justify-between xl:p-16">
            <div class="absolute inset-0 opacity-40" aria-hidden="true" style="background-image: linear-gradient(rgba(148,163,184,.08) 1px, transparent 1px), linear-gradient(90deg, rgba(148,163,184,.08) 1px, transparent 1px); background-size: 48px 48px;"></div>
            <div class="absolute -top-32 -left-24 size-96 rounded-full bg-teal-500/20 blur-3xl" aria-hidden="true"></div>
            <div class="absolute -right-32 bottom-0 size-[30rem] rounded-full bg-cyan-600/10 blur-3xl" aria-hidden="true"></div>

            <div class="relative flex items-center gap-3">
                <div class="grid size-12 place-items-center rounded-2xl border border-white/10 bg-white/10 text-teal-300 backdrop-blur">
                    <svg class="size-7" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M7.5 8.5h9M7.5 12h6M9 19l-4 2v-4.5A7.5 7.5 0 0 1 4 13V8.5A4.5 4.5 0 0 1 8.5 4h7A4.5 4.5 0 0 1 20 8.5V13a4.5 4.5 0 0 1-4.5 4.5H12L9 19Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <div><p class="font-bold">سامانه تیکتینگ</p><p class="mt-0.5 text-xs text-slate-400">مرکز یکپارچه پشتیبانی</p></div>
            </div>

            <div class="relative max-w-xl">
                <div class="mb-8 flex items-center gap-3 text-sm font-bold text-teal-300"><span class="h-px w-10 bg-teal-400"></span>تجربه‌ای سریع، شفاف و قابل اعتماد</div>
                <h2 class="text-5xl font-black leading-[1.35] tracking-tight xl:text-6xl">هر درخواست،<br><span class="text-teal-300">یک پاسخ روشن.</span></h2>
                <p class="mt-7 max-w-lg text-base leading-8 text-slate-300">تمام گفت‌وگوها، اولویت‌ها و مسیر رسیدگی در یک فضای متمرکز؛ برای پشتیبانی دقیق‌تر و تصمیم‌گیری سریع‌تر.</p>

                <div class="mt-12 grid grid-cols-3 gap-3">
                    <div class="rounded-2xl border border-white/10 bg-white/[0.06] p-4 backdrop-blur-sm">
                        <svg class="mb-4 size-6 text-teal-300" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 12a8 8 0 1 0 16 0 8 8 0 0 0-16 0Zm8-4v4l2.5 2.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                        <p class="text-sm font-bold">پاسخ‌گویی سریع</p><p class="mt-1 text-xs text-slate-400">مدیریت متمرکز</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/[0.06] p-4 backdrop-blur-sm">
                        <svg class="mb-4 size-6 text-teal-300" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m5 13 4 4L19 7" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <p class="text-sm font-bold">پیگیری دقیق</p><p class="mt-1 text-xs text-slate-400">تاریخچه شفاف</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/[0.06] p-4 backdrop-blur-sm">
                        <svg class="mb-4 size-6 text-teal-300" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3 4.5 6v5.5c0 4.4 3.2 8.1 7.5 9.5 4.3-1.4 7.5-5.1 7.5-9.5V6L12 3Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>
                        <p class="text-sm font-bold">دسترسی امن</p><p class="mt-1 text-xs text-slate-400">حفاظت از داده</p>
                    </div>
                </div>
            </div>

            <div class="relative flex items-center justify-between border-t border-white/10 pt-6 text-xs text-slate-500"><span>نسخه ۱.۰</span><span>پشتیبانی، ساده و یکپارچه</span></div>
        </aside>
    </main>
</body>
</html>
