<x-app-layout title="ثبت سامانه جدید" description="ایجاد اتصال جدید و صدور API Key اختصاصی">
    <div class="mb-6 flex items-center gap-2 text-xs font-bold text-slate-400">
        <a href="{{ route('systems.index') }}" class="transition hover:text-cyan-700">مدیریت سامانه‌ها</a>
        <svg class="size-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m15 18-6-6 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
        <span class="text-slate-700">ثبت سامانه</span>
    </div>

    <form method="POST" action="{{ route('systems.store') }}">
        @csrf
        @include('systems._form', ['submitLabel' => 'ایجاد سامانه و کلید'])
    </form>
</x-app-layout>
