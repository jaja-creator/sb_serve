@props([
    'title',
    'description',
])

<div class="flex w-full flex-col items-center text-center">
    <flux:heading size="xl">{{ $title }}</flux:heading>
    <span class="h-0.5 w-8 bg-seal-gold rounded-full my-2"></span>
    <flux:subheading>{{ $description }}</flux:subheading>
</div>
