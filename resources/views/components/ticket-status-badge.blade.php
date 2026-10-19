@props(['status'])

@php
    $code = mb_strtolower($status?->code ?? 'unknown');
    $classes = match ($code) {
        'open', 'new' => 'bg-blue-50 text-blue-700 ring-blue-600/15',
        'pending', 'waiting', 'in_progress' => 'bg-amber-50 text-amber-700 ring-amber-600/15',
        'resolved' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/15',
        'closed' => 'bg-slate-100 text-slate-600 ring-slate-500/15',
        'cancelled' => 'bg-rose-50 text-rose-700 ring-rose-600/15',
        default => 'bg-violet-50 text-violet-700 ring-violet-600/15',
    };
@endphp

<span {{ $attributes->class("inline-flex items-center gap-2 rounded-full px-3 py-1.5 text-[11px] font-black ring-1 ring-inset {$classes}") }}>
    <span class="size-1.5 rounded-full bg-current opacity-70"></span>
    {{ $status?->name ?? 'نامشخص' }}
</span>
