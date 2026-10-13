@props(['class' => '', 'size' => 96])
<img
    src="{{ asset('images/allacapan-seal.png') }}"
    alt="Seal of the Municipality of Allacapan, Cagayan"
    style="width: {{ (int) $size }}px; height: {{ (int) $size }}px; flex-shrink: 0;"
    {{ $attributes->merge(['class' => trim('rounded-full object-cover shadow-lg border-4 border-parchment/30 ' . $class)]) }}
>