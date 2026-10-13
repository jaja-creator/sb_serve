@props(['status'])

@php
$colors = \App\Support\StatusColors::pill($status);
@endphp

<span
    {{ $attributes->merge(['class' => 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium tracking-wide']) }}
    style="background-color: {{ $colors['bg'] }}; color: {{ $colors['text'] }};"
>
    {{ $status }}
</span>
