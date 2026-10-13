<x-app-layout title="داشبورد" description="نمای کلی فضای کاری و دسترسی سریع به بخش‌های سامانه">
    <section class="relative overflow-hidden rounded-3xl bg-slate-950 p-7 text-white shadow-xl shadow-slate-950/10 sm:p-10">
        <div class="pointer-events-none absolute -left-24 -top-32 size-96 rounded-full bg-teal-500/20 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-40 right-1/3 size-96 rounded-full bg-cyan-600/10 blur-3xl"></div>
        <div class="relative flex flex-col gap-8 lg:flex-row lg:items-center lg:justify-between">
            <div class="max-w-2xl">
                <span class="inline-flex items-center gap-2 rounded-full border border-teal-400/20 bg-teal-400/10 px-3 py-1.5 text-xs font-bold text-teal-200">
                    <span class="size-2 rounded-full bg-teal-400"></span>
                    نشست فعال و ایمن
                </span>
                <h2 class="mt-5 text-2xl font-black leading-relaxed sm:text-3xl">سلام {{ auth()->user()->name }}، خوش آمدید.</h2>
                <p class="mt-3 max-w-xl text-sm leading-7 text-slate-400">از این داشبورد می‌توانید بخش‌های مختلف سامانه را مدیریت و وضعیت عملیات را پیگیری کنید.</p>
            </div>
            @can('viewAny', App\Models\User::class)
                <a href="{{ route('users.index') }}" class="inline-flex h-12 shrink-0 items-center justify-center gap-2 rounded-2xl bg-white px-5 text-sm font-black text-slate-950 shadow-lg transition hover:-translate-y-0.5 hover:bg-teal-50">
                    مدیریت کاربران
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m15 18-6-6 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </a>
            @endcan
        </div>
    </section>

    <section class="mt-6 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <span class="grid size-12 place-items-center rounded-2xl bg-teal-50 text-teal-700"><svg class="size-6" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 13h6V4H4v9Zm0 7h6v-4H4v4Zm10 0h6v-9h-6v9Zm0-12h6V4h-6v4Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg></span>
            <h3 class="mt-5 font-black text-slate-900">فضای کاری یکپارچه</h3>
            <p class="mt-2 text-sm leading-7 text-slate-500">تمام ابزارهای پشتیبانی در یک محیط منظم و سریع در دسترس شماست.</p>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <span class="grid size-12 place-items-center rounded-2xl bg-blue-50 text-blue-700"><svg class="size-6" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3 4.5 6v5.5c0 4.4 3.2 8.1 7.5 9.5 4.3-1.4 7.5-5.1 7.5-9.5V6L12 3Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg></span>
            <h3 class="mt-5 font-black text-slate-900">کنترل دسترسی امن</h3>
            <p class="mt-2 text-sm leading-7 text-slate-500">نشست‌ها، وضعیت کاربران و دسترسی‌های مدیریتی به‌صورت امن کنترل می‌شوند.</p>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm md:col-span-2 xl:col-span-1">
            <span class="grid size-12 place-items-center rounded-2xl bg-violet-50 text-violet-700"><svg class="size-6" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 19V9m7 10V5m7 14v-7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></span>
            <h3 class="mt-5 font-black text-slate-900">گزارش‌پذیری شفاف</h3>
            <p class="mt-2 text-sm leading-7 text-slate-500">تغییرات مهم ثبت می‌شوند تا تاریخچه فعالیت‌ها همیشه قابل پیگیری باشد.</p>
        </div>
    </section>
</x-app-layout>
