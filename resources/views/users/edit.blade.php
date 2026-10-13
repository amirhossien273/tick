<x-app-layout title="ویرایش کاربر" description="به‌روزرسانی اطلاعات حساب {{ $user->name }}">
    <div class="mb-6 flex items-center gap-2 text-xs font-bold text-slate-400">
        <a href="{{ route('users.index') }}" class="transition hover:text-teal-700">مدیریت کاربران</a>
        <svg class="size-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m15 18-6-6 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
        <a href="{{ route('users.show', $user) }}" class="transition hover:text-teal-700">{{ $user->name }}</a>
        <svg class="size-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m15 18-6-6 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
        <span class="text-slate-700">ویرایش</span>
    </div>

    <form method="POST" action="{{ route('users.update', $user) }}">
        @csrf
        @method('PUT')
        @include('users._form', ['submitLabel' => 'ذخیره تغییرات'])
    </form>
</x-app-layout>
