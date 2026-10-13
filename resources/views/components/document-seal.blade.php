@props(['status'])

@php
$colors = match($status) {
    'Enacted' => ['bg' => '#1B4332', 'text' => '#FFFFFF', 'border' => '#1B4332'],
    'Approved' => ['bg' => '#FFFFFF', 'text' => '#1B4332', 'border' => '#1B4332'],
    'For Plenary Deliberation' => ['bg' => '#FFFFFF', 'text' => '#1B4332', 'border' => '#1B4332'],
    'Under Committee Review' => ['bg' => '#FFFFFF', 'text' => '#8A6D1A', 'border' => '#C9A227'],
    'Filed' => ['bg' => '#FFFFFF', 'text' => '#8A6D1A', 'border' => '#C9A227'],
    'Vetoed' => ['bg' => '#8B2635', 'text' => '#FFFFFF', 'border' => '#8B2635'],
    'Returned' => ['bg' => '#FFFFFF', 'text' => '#8B2635', 'border' => '#8B2635'],
    default => ['bg' => '#FFFFFF', 'text' => '#6B7A75', 'border' => '#D1D5DB'],
};
@endphp

<div {{ $attributes->merge(['class' => 'inline-flex']) }}>
    <span
        class="px-3 py-1.5 rounded-full text-xs font-semibold uppercase tracking-wide whitespace-nowrap"
        style="background-color: {{ $colors['bg'] }}; color: {{ $colors['text'] }}; border: 1px solid {{ $colors['border'] }};"
    >
        {{ $status }}
    </span>
</div>