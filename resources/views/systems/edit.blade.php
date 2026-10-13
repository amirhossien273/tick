<x-app-layout title="ویرایش سامانه" description="به‌روزرسانی مشخصات {{ $system->name }}">
    <div class="mb-6 flex items-center gap-2 text-xs font-bold text-slate-400">
        <a href="{{ route('systems.index') }}" class="transition hover:text-cyan-700">مدیریت سامانه‌ها</a>
        <svg class="size-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m15 18-6-6 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
        <a href="{{ route('systems.show', $system) }}" class="transition hover:text-cyan-700">{{ $system->name }}</a>
        <svg class="size-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m15 18-6-6 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
        <span class="text-slate-700">ویرایش</span>
    </div>

    <form method="POST" action="{{ route('systems.update', $system) }}">
        @csrf
        @method('PUT')
        @include('systems._form', ['submitLabel' => 'ذخیره تغییرات'])
    </form>
</x-app-layout>
