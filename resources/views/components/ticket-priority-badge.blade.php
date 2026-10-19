@props(['priority'])

@php
    $code = mb_strtolower($priority?->code ?? 'unknown');
    $classes = match ($code) {
        'urgent', 'critical' => 'bg-rose-50 text-rose-700 ring-rose-600/15',
        'high' => 'bg-orange-50 text-orange-700 ring-orange-600/15',
        'medium', 'normal' => 'bg-amber-50 text-amber-700 ring-amber-600/15',
        'low' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/15',
        default => 'bg-slate-100 text-slate-600 ring-slate-500/15',
    };
@endphp

<span {{ $attributes->class("inline-flex items-center rounded-full px-3 py-1.5 text-[11px] font-black ring-1 ring-inset {$classes}") }}>
    {{ $priority?->name ?? 'نامشخص' }}
</span>
