<x-app-layout title="ثبت کاربر جدید" description="ایجاد حساب جدید و تعیین اطلاعات ورود کاربر">
    <div class="mb-6 flex items-center gap-2 text-xs font-bold text-slate-400">
        <a href="{{ route('users.index') }}" class="transition hover:text-teal-700">مدیریت کاربران</a>
        <svg class="size-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m15 18-6-6 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
        <span class="text-slate-700">ثبت کاربر</span>
    </div>

    <form method="POST" action="{{ route('users.store') }}">
        @csrf
        @include('users._form', ['submitLabel' => 'ایجاد حساب کاربری'])
    </form>
</x-app-layout>
