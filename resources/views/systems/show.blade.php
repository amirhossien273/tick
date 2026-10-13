<x-app-layout title="جزئیات سامانه" description="مشاهده دسترسی، API Key و تاریخچه {{ $system->name }}">
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-2 text-xs font-bold text-slate-400">
            <a href="{{ route('systems.index') }}" class="transition hover:text-cyan-700">مدیریت سامانه‌ها</a>
            <svg class="size-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m15 18-6-6 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
            <span class="text-slate-700">{{ $system->name }}</span>
        </div>
        <a href="{{ route('systems.edit', $system) }}" class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-slate-950 px-4 text-xs font-black text-white shadow-lg transition hover:bg-cyan-700">
            <svg class="size-4.5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m14 5 5 5M4 20l4.5-1 10-10a2.1 2.1 0 0 0-3-3l-10 10L4 20Z" stroke="currentColor" stroke-width="1.7"/></svg>
            ویرایش سامانه
        </a>
    </div>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_380px]">
        <div class="flex min-w-0 flex-col gap-6">
            <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="relative overflow-hidden bg-slate-950 p-6 text-white sm:p-8">
                    <div class="pointer-events-none absolute -left-16 -top-20 size-64 rounded-full bg-cyan-500/20 blur-3xl"></div>
                    <div class="relative flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex min-w-0 items-center gap-4">
                            <span class="grid size-16 shrink-0 place-items-center rounded-2xl border border-white/10 bg-white/10 text-cyan-200"><svg class="size-8" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 4h14a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Zm0 9h14a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="1.7"/></svg></span>
                            <div class="min-w-0"><h2 class="truncate text-xl font-black">{{ $system->name }}</h2><p class="mt-2 truncate font-mono text-xs font-bold tracking-wide text-slate-400" dir="ltr">{{ $system->code }}</p></div>
                        </div>
                        <span @class(['inline-flex w-fit items-center gap-2 rounded-full px-3 py-1.5 text-xs font-bold', 'bg-emerald-400/10 text-emerald-300 ring-1 ring-inset ring-emerald-400/20' => $system->is_active, 'bg-rose-400/10 text-rose-300 ring-1 ring-inset ring-rose-400/20' => ! $system->is_active])><span @class(['size-2 rounded-full', 'bg-emerald-400' => $system->is_active, 'bg-rose-400' => ! $system->is_active])></span>{{ $system->is_active ? 'دسترسی فعال' : 'دسترسی غیرفعال' }}</span>
                    </div>
                </div>

                <dl class="grid gap-px bg-slate-100 sm:grid-cols-2">
                    <div class="bg-white p-5 sm:p-6"><dt class="text-xs font-bold text-slate-400">ایجادکننده</dt><dd class="mt-2 text-sm font-black text-slate-800">{{ $system->creator->name }}</dd><dd class="mt-1 text-xs text-slate-400" dir="ltr">{{ $system->creator->email }}</dd></div>
                    <div class="bg-white p-5 sm:p-6"><dt class="text-xs font-bold text-slate-400">زمان ایجاد</dt><dd class="mt-2 text-sm font-black text-slate-800">{{ $system->created_at->format('Y/m/d - H:i') }}</dd></div>
                    <div class="bg-white p-5 sm:p-6"><dt class="text-xs font-bold text-slate-400">آخرین درخواست معتبر</dt><dd class="mt-2 text-sm font-black text-slate-800">{{ $system->last_used_at?->format('Y/m/d - H:i') ?? 'تاکنون درخواستی ثبت نشده' }}</dd></div>
                    <div class="bg-white p-5 sm:p-6"><dt class="text-xs font-bold text-slate-400">آخرین ویرایش</dt><dd class="mt-2 text-sm font-black text-slate-800">{{ $system->updated_at->format('Y/m/d - H:i') }}</dd></div>
                </dl>

                <div class="border-t border-slate-100 p-5 sm:p-6">
                    <p class="text-xs font-bold text-slate-400">توضیحات سامانه</p>
                    <p class="mt-3 whitespace-pre-line text-sm leading-8 text-slate-600">{{ $system->description ?: 'توضیحی برای این سامانه ثبت نشده است.' }}</p>
                </div>
            </section>

            <section class="relative overflow-hidden rounded-3xl border border-violet-200 bg-gradient-to-br from-violet-50 to-white p-6 shadow-sm sm:p-7">
                <div class="pointer-events-none absolute -left-16 -top-16 size-48 rounded-full bg-violet-200/40 blur-3xl"></div>
                <div class="relative flex flex-col gap-5">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-start gap-3">
                            <span class="grid size-11 shrink-0 place-items-center rounded-2xl bg-violet-100 text-violet-700"><svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M14.5 7.5a4 4 0 1 0-5.66 5.66L10 14.32V17h2v2h3v-3.68l1.16-1.16a4 4 0 0 0-1.66-6.66Z" stroke="currentColor" stroke-width="1.7"/></svg></span>
                            <div><h2 class="font-black text-violet-950">API Key اختصاصی</h2><p class="mt-1 text-xs leading-6 text-violet-700/80">این کلید محرمانه است؛ فقط در اختیار سرویس مجاز قرار دهید.</p></div>
                        </div>
                        @if (session('api_key_revealed'))<span class="rounded-full bg-emerald-100 px-3 py-1.5 text-[10px] font-black text-emerald-700">کلید جدید</span>@endif
                    </div>

                    <div class="flex flex-col gap-2 sm:flex-row">
                        <div class="relative min-w-0 flex-1">
                            <input type="{{ session('api_key_revealed') ? 'text' : 'password' }}" value="{{ $system->api_key }}" readonly dir="ltr" class="h-13 w-full rounded-2xl border border-violet-200 bg-white px-4 pl-12 font-mono text-xs text-slate-700 outline-none" data-secret-input>
                            <button type="button" class="absolute inset-y-0 left-0 grid w-12 place-items-center text-violet-500 transition hover:text-violet-800" aria-label="نمایش API Key" aria-pressed="{{ session('api_key_revealed') ? 'true' : 'false' }}" data-secret-toggle><svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z" stroke="currentColor" stroke-width="1.7"/><circle cx="12" cy="12" r="2.5" stroke="currentColor" stroke-width="1.7"/></svg></button>
                        </div>
                        <button type="button" class="inline-flex h-13 shrink-0 items-center justify-center gap-2 rounded-2xl bg-violet-700 px-5 text-xs font-black text-white shadow-lg shadow-violet-700/15 transition hover:bg-violet-800" data-copy-secret>
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M8 8h11v11H8V8Zm-3 8H4V5h11v1" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>
                            <span data-copy-label>کپی کلید</span>
                        </button>
                    </div>

                    <div class="flex items-start gap-2 rounded-2xl bg-amber-50 px-4 py-3 text-xs leading-6 text-amber-800 ring-1 ring-inset ring-amber-200"><svg class="mt-1 size-4 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 8v4m0 4h.01M10.3 4.45 3.42 16.2A2 2 0 0 0 5.15 19h13.7a2 2 0 0 0 1.73-2.8L13.7 4.45a2 2 0 0 0-3.4 0Z" stroke="currentColor" stroke-width="1.7"/></svg><span>با صدور کلید جدید، کلید قبلی بلافاصله نامعتبر خواهد شد.</span></div>
                </div>
            </section>

            <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-7">
                <div class="flex items-center justify-between gap-4"><div><h2 class="font-black text-slate-950">تاریخچه فعالیت‌ها</h2><p class="mt-2 text-xs text-slate-500">آخرین تغییرات ثبت‌شده توسط Activity Log</p></div><span class="grid size-10 place-items-center rounded-xl bg-slate-100 text-slate-600"><svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 7v5l3 2m6-2a9 9 0 1 1-3-6.7" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg></span></div>
                <div class="mt-6 flex flex-col gap-1">
                    @forelse ($system->activities as $activity)
                        <div class="flex gap-4 rounded-2xl p-3 transition hover:bg-slate-50"><div class="flex flex-col items-center"><span class="mt-1 size-2.5 rounded-full bg-cyan-500 ring-4 ring-cyan-50"></span><span class="mt-2 h-full w-px bg-slate-100"></span></div><div class="min-w-0 pb-4"><p class="text-sm font-bold text-slate-800">{{ match ($activity->event) { 'created' => 'سامانه و کلید اولیه ایجاد شدند.', 'updated' => 'اطلاعات یا وضعیت سامانه تغییر کرد.', 'api_key_rotated' => 'API Key جدید صادر شد.', default => $activity->description } }}</p><p class="mt-1.5 text-xs text-slate-400">{{ $activity->created_at->format('Y/m/d - H:i') }}</p></div></div>
                    @empty
                        <div class="rounded-2xl bg-slate-50 p-5 text-center text-sm text-slate-500">فعالیتی برای این سامانه ثبت نشده است.</div>
                    @endforelse
                </div>
            </section>
        </div>

        <aside class="flex flex-col gap-5">
            <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="font-black text-slate-950">کنترل دسترسی API</h2>
                <p class="mt-2 text-sm leading-7 text-slate-500">با غیرفعال‌سازی، درخواست‌های این سامانه نباید معتبر شناخته شوند.</p>
                <form method="POST" action="{{ route('systems.status.update', $system) }}" class="mt-5" data-confirm="{{ $system->is_active ? 'دسترسی API این سامانه غیرفعال شود؟' : 'دسترسی API این سامانه دوباره فعال شود؟' }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="is_active" value="{{ $system->is_active ? 0 : 1 }}">
                    <button type="submit" @class(['inline-flex h-12 w-full items-center justify-center rounded-2xl text-sm font-black transition', 'bg-rose-50 text-rose-700 hover:bg-rose-100' => $system->is_active, 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' => ! $system->is_active])>{{ $system->is_active ? 'غیرفعال‌کردن دسترسی' : 'فعال‌کردن دسترسی' }}</button>
                </form>
            </section>

            <section class="rounded-3xl border border-violet-100 bg-violet-50 p-6">
                <h2 class="text-sm font-black text-violet-950">صدور API Key جدید</h2>
                <p class="mt-2 text-xs leading-6 text-violet-800/80">در صورت افشا یا نیاز به چرخش کلید، یک کلید کاملاً جدید صادر کنید.</p>
                <form method="POST" action="{{ route('systems.api-key.store', $system) }}" class="mt-4" data-confirm="کلید فعلی بلافاصله نامعتبر می‌شود. از صدور کلید جدید مطمئن هستید؟">
                    @csrf
                    <button type="submit" class="inline-flex h-11 w-full items-center justify-center gap-2 rounded-xl bg-violet-700 px-4 text-xs font-black text-white shadow-lg shadow-violet-700/15 transition hover:bg-violet-800"><svg class="size-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M20 12a8 8 0 1 1-2.34-5.66M20 4v5h-5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>صدور کلید جدید</button>
                </form>
            </section>

            <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-xs font-bold text-slate-400">شناسه داخلی</p>
                <p class="mt-3 break-all font-mono text-xs font-bold leading-6 text-slate-600" dir="ltr">{{ $system->id }}</p>
            </section>
        </aside>
    </div>
</x-app-layout>
