@php
    $isEditing = isset($system);
@endphp

<div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
        <div class="mb-7 flex items-start gap-4 border-b border-slate-100 pb-6">
            <span class="grid size-12 shrink-0 place-items-center rounded-2xl bg-cyan-50 text-cyan-700">
                <svg class="size-6" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 4h14a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Zm0 9h14a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2Zm2-5h.01M7 17h.01" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
            </span>
            <div>
                <h2 class="font-black text-slate-950">مشخصات سامانه</h2>
                <p class="mt-2 text-sm leading-6 text-slate-500">نام و توضیحات سامانه متصل‌شونده را وارد کنید؛ کد یکتا به‌صورت خودکار صادر می‌شود.</p>
            </div>
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="name" class="mb-2 block text-sm font-bold text-slate-700">نام سامانه <span class="text-rose-500">*</span></label>
                <input id="name" name="name" type="text" value="{{ old('name', $system->name ?? '') }}" required autofocus
                       class="h-13 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none transition placeholder:text-slate-400 hover:border-slate-300 focus:border-cyan-500 focus:bg-white focus:ring-4 focus:ring-cyan-500/10"
                       placeholder="مثلاً فروشگاه آنلاین">
                @error('name')<p class="mt-2 text-xs font-bold text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <span class="mb-2 block text-sm font-bold text-slate-700">کد یکتای سامانه</span>
                <div class="flex h-13 items-center justify-between gap-3 rounded-2xl border border-dashed border-cyan-200 bg-cyan-50/70 px-4">
                    <span class="min-w-0 truncate font-mono text-sm font-black tracking-wide text-cyan-900" dir="ltr">
                        {{ $isEditing ? $system->code : 'SYS-'.now()->format('Ydm').'-XXXX' }}
                    </span>
                    <span class="shrink-0 rounded-lg bg-white px-2.5 py-1 text-[10px] font-black text-cyan-700 shadow-sm">
                        {{ $isEditing ? 'غیرقابل تغییر' : 'تولید خودکار' }}
                    </span>
                </div>
                <p class="mt-2 text-[11px] leading-5 text-slate-400">شمارنده انتهایی در ابتدای هر سال از ۰۰۰۱ شروع می‌شود.</p>
            </div>
        </div>

        <div class="mt-5">
            <label for="description" class="mb-2 block text-sm font-bold text-slate-700">توضیحات</label>
            <textarea id="description" name="description" rows="6" maxlength="2000"
                      class="w-full resize-y rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm leading-7 outline-none transition placeholder:text-slate-400 hover:border-slate-300 focus:border-cyan-500 focus:bg-white focus:ring-4 focus:ring-cyan-500/10"
                      placeholder="کاربرد، مالک و جزئیات ارتباط این سامانه را بنویسید...">{{ old('description', $system->description ?? '') }}</textarea>
            @error('description')<p class="mt-2 text-xs font-bold text-rose-600">{{ $message }}</p>@enderror
        </div>
    </div>

    <aside class="flex flex-col gap-5">
        @unless ($isEditing)
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-black text-slate-900">وضعیت اولیه دسترسی</p>
                <label class="mt-4 flex cursor-pointer items-center justify-between gap-4 rounded-2xl border border-slate-200 bg-slate-50 p-4 transition hover:border-cyan-200">
                    <span>
                        <span class="block text-sm font-bold text-slate-800">دسترسی API فعال باشد</span>
                        <span class="mt-1 block text-xs leading-5 text-slate-500">کلید بلافاصله قابل استفاده است</span>
                    </span>
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true)) class="size-5 rounded border-slate-300 accent-cyan-600 focus:ring-cyan-500">
                </label>
            </div>
        @endunless

        <div class="rounded-3xl border border-violet-100 bg-violet-50 p-5">
            <div class="flex items-start gap-3">
                <span class="grid size-10 shrink-0 place-items-center rounded-xl bg-violet-100 text-violet-700"><svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M14.5 7.5a4 4 0 1 0-5.66 5.66L10 14.32V17h2v2h3v-3.68l1.16-1.16a4 4 0 0 0-1.66-6.66Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg></span>
                <div><p class="text-sm font-black text-violet-950">API Key اختصاصی</p><p class="mt-2 text-xs leading-6 text-violet-800/80">هنگام ثبت، یک کلید تصادفی امن تولید و به‌صورت رمزنگاری‌شده ذخیره می‌شود. کلید از صفحه جزئیات قابل مشاهده است.</p></div>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="inline-flex h-12 flex-1 items-center justify-center gap-2 rounded-2xl bg-slate-950 px-5 text-sm font-black text-white shadow-lg shadow-slate-950/15 transition hover:-translate-y-0.5 hover:bg-cyan-700 focus:outline-none focus-visible:ring-4 focus-visible:ring-cyan-500/20">{{ $submitLabel }}</button>
            <a href="{{ $isEditing ? route('systems.show', $system) : route('systems.index') }}" class="inline-flex h-12 items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 text-sm font-bold text-slate-600 transition hover:border-slate-300 hover:text-slate-900">انصراف</a>
        </div>
    </aside>
</div>
