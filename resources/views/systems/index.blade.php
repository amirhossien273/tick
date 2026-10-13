<x-app-layout title="مدیریت سامانه‌ها" description="مدیریت اتصال سامانه‌های خارجی، کلیدها و دسترسی API">
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <p class="text-sm leading-7 text-slate-500">سامانه‌های متصل را جستجو کنید و وضعیت دسترسی آن‌ها را مدیریت کنید.</p>
        <a href="{{ route('systems.create') }}" class="inline-flex h-12 items-center justify-center gap-2 rounded-2xl bg-slate-950 px-5 text-sm font-black text-white shadow-lg shadow-slate-950/15 transition hover:-translate-y-0.5 hover:bg-cyan-700">
            <svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/></svg>
            ثبت سامانه جدید
        </a>
    </div>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="آمار سامانه‌ها">
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between gap-4"><span class="grid size-11 place-items-center rounded-2xl bg-slate-100 text-slate-700"><svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 4h14a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Zm0 9h14a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="1.7"/></svg></span><span class="text-2xl font-black text-slate-950">{{ number_format($statistics['total']) }}</span></div>
            <p class="mt-4 text-xs font-bold text-slate-500">کل سامانه‌ها</p>
        </div>
        <div class="rounded-3xl border border-emerald-100 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between gap-4"><span class="grid size-11 place-items-center rounded-2xl bg-emerald-50 text-emerald-700"><svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m5 13 4 4L19 7" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/></svg></span><span class="text-2xl font-black text-slate-950">{{ number_format($statistics['active']) }}</span></div>
            <p class="mt-4 text-xs font-bold text-slate-500">دسترسی فعال</p>
        </div>
        <div class="rounded-3xl border border-rose-100 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between gap-4"><span class="grid size-11 place-items-center rounded-2xl bg-rose-50 text-rose-700"><svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6.5 6.5 17.5 17.5M18 12a6 6 0 1 1-12 0 6 6 0 0 1 12 0Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg></span><span class="text-2xl font-black text-slate-950">{{ number_format($statistics['inactive']) }}</span></div>
            <p class="mt-4 text-xs font-bold text-slate-500">دسترسی مسدود</p>
        </div>
        <div class="rounded-3xl border border-cyan-100 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between gap-4"><span class="grid size-11 place-items-center rounded-2xl bg-cyan-50 text-cyan-700"><svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 7v5l3 2m6-2a9 9 0 1 1-3-6.7" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg></span><span class="text-2xl font-black text-slate-950">{{ number_format($statistics['recently_used']) }}</span></div>
            <p class="mt-4 text-xs font-bold text-slate-500">استفاده در ۳۰ روز اخیر</p>
        </div>
    </section>

    <section class="mt-6 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
        <form method="GET" action="{{ route('systems.index') }}" class="flex flex-col gap-3 border-b border-slate-100 p-4 sm:flex-row sm:items-center sm:p-5">
            <div class="relative min-w-0 flex-1">
                <span class="pointer-events-none absolute inset-y-0 right-0 grid w-11 place-items-center text-slate-400"><svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.7"/><path d="m16 16 4 4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg></span>
                <input name="search" type="search" value="{{ $filters['search'] ?? '' }}" class="h-11 w-full rounded-xl border border-slate-200 bg-slate-50 pr-11 pl-4 text-sm outline-none transition placeholder:text-slate-400 focus:border-cyan-500 focus:bg-white focus:ring-4 focus:ring-cyan-500/10" placeholder="جستجو در نام، کد یا توضیحات...">
            </div>
            <select name="status" class="h-11 rounded-xl border border-slate-200 bg-slate-50 px-4 text-sm font-bold text-slate-600 outline-none transition focus:border-cyan-500 focus:bg-white focus:ring-4 focus:ring-cyan-500/10">
                <option value="">همه وضعیت‌ها</option>
                <option value="active" @selected(($filters['status'] ?? '') === 'active')>فقط فعال</option>
                <option value="inactive" @selected(($filters['status'] ?? '') === 'inactive')>فقط غیرفعال</option>
            </select>
            <button type="submit" class="inline-flex h-11 items-center justify-center rounded-xl bg-slate-900 px-5 text-sm font-bold text-white transition hover:bg-cyan-700">اعمال فیلتر</button>
            @if (($filters['search'] ?? null) || ($filters['status'] ?? null))<a href="{{ route('systems.index') }}" class="inline-flex h-11 items-center justify-center rounded-xl px-3 text-sm font-bold text-slate-500 transition hover:bg-slate-100 hover:text-slate-900">پاک‌کردن</a>@endif
        </form>

        @if ($systems->isEmpty())
            <div class="grid min-h-80 place-items-center p-8 text-center">
                <div><span class="mx-auto grid size-16 place-items-center rounded-3xl bg-slate-100 text-slate-400"><svg class="size-7" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 4h14a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="1.7"/></svg></span><h2 class="mt-5 font-black text-slate-900">سامانه‌ای پیدا نشد</h2><p class="mt-2 text-sm text-slate-500">عبارت جستجو یا فیلترها را تغییر دهید.</p></div>
            </div>
        @else
            <div class="hidden overflow-x-auto md:block">
                <table class="w-full text-right">
                    <thead class="bg-slate-50 text-[11px] font-black text-slate-500"><tr><th class="px-6 py-4">سامانه</th><th class="px-5 py-4">وضعیت API</th><th class="px-5 py-4">آخرین استفاده</th><th class="px-5 py-4">ایجادکننده</th><th class="px-6 py-4 text-left">عملیات</th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($systems as $system)
                            <tr class="group transition hover:bg-slate-50/80">
                                <td class="px-6 py-4"><div class="flex items-center gap-3"><span class="grid size-10 shrink-0 place-items-center rounded-xl bg-cyan-50 text-cyan-700 transition group-hover:bg-cyan-100"><svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 5h14v6H5V5Zm0 9h14v5H5v-5Z" stroke="currentColor" stroke-width="1.6"/></svg></span><div class="min-w-0"><a href="{{ route('systems.show', $system) }}" class="block truncate text-sm font-black text-slate-900 transition hover:text-cyan-700">{{ $system->name }}</a><span class="mt-1 block truncate font-mono text-[11px] font-bold text-slate-400" dir="ltr">{{ $system->code }}</span></div></div></td>
                                <td class="px-5 py-4"><span @class(['inline-flex items-center gap-2 rounded-full px-3 py-1.5 text-[11px] font-bold', 'bg-emerald-50 text-emerald-700' => $system->is_active, 'bg-rose-50 text-rose-700' => ! $system->is_active])><span @class(['size-1.5 rounded-full', 'bg-emerald-500' => $system->is_active, 'bg-rose-500' => ! $system->is_active])></span>{{ $system->is_active ? 'فعال' : 'غیرفعال' }}</span></td>
                                <td class="px-5 py-4 text-xs text-slate-500">{{ $system->last_used_at?->format('Y/m/d - H:i') ?? 'بدون درخواست' }}</td>
                                <td class="px-5 py-4 text-xs font-bold text-slate-600">{{ $system->creator->name }}</td>
                                <td class="px-6 py-4"><div class="flex items-center justify-end gap-1"><a href="{{ route('systems.show', $system) }}" class="grid size-9 place-items-center rounded-xl text-slate-400 transition hover:bg-cyan-50 hover:text-cyan-700" aria-label="مشاهده {{ $system->name }}"><svg class="size-4.5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z" stroke="currentColor" stroke-width="1.7"/><circle cx="12" cy="12" r="2.5" stroke="currentColor" stroke-width="1.7"/></svg></a><a href="{{ route('systems.edit', $system) }}" class="grid size-9 place-items-center rounded-xl text-slate-400 transition hover:bg-blue-50 hover:text-blue-700" aria-label="ویرایش {{ $system->name }}"><svg class="size-4.5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m14 5 5 5M4 20l4.5-1 10-10a2.1 2.1 0 0 0-3-3l-10 10L4 20Z" stroke="currentColor" stroke-width="1.7"/></svg></a></div></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="divide-y divide-slate-100 md:hidden">
                @foreach ($systems as $system)
                    <a href="{{ route('systems.show', $system) }}" class="flex items-center gap-3 p-4 transition hover:bg-slate-50"><span class="grid size-11 shrink-0 place-items-center rounded-xl bg-cyan-50 text-cyan-700"><svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 5h14v6H5V5Zm0 9h14v5H5v-5Z" stroke="currentColor" stroke-width="1.6"/></svg></span><span class="min-w-0 flex-1"><span class="block truncate text-sm font-black text-slate-900">{{ $system->name }}</span><span class="mt-1 block truncate font-mono text-[11px] text-slate-400" dir="ltr">{{ $system->code }}</span></span><span @class(['size-2.5 rounded-full', 'bg-emerald-500' => $system->is_active, 'bg-rose-500' => ! $system->is_active])></span><svg class="size-4 text-slate-300" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m15 18-6-6 6-6" stroke="currentColor" stroke-width="1.8"/></svg></a>
                @endforeach
            </div>

            @if ($systems->hasPages())<div class="border-t border-slate-100 px-5 py-4">{{ $systems->onEachSide(1)->links() }}</div>@endif
        @endif
    </section>
</x-app-layout>
