<div class="flex items-start gap-8 max-md:flex-col">
    <div class="me-2 w-full pb-4 md:w-[200px]">
        <p class="text-[10px] uppercase tracking-widest text-seal-gold font-mono font-semibold mb-3 border-b border-seal-gold/40 pb-1">
            Personnel File
        </p>
        <nav aria-label="{{ __('Settings') }}" class="settings-tabs">
            <a href="{{ route('profile.edit') }}" wire:navigate class="settings-tab {{ request()->routeIs('profile.edit') ? 'settings-tab-active' : '' }}">
                {{ __('Profile') }}
            </a>
            <a href="{{ route('security.edit') }}" wire:navigate class="settings-tab {{ request()->routeIs('security.edit') ? 'settings-tab-active' : '' }}">
                {{ __('Security') }}
            </a>
        </nav>
    </div>

    <flux:separator class="md:hidden" />

    <div class="flex-1 self-stretch max-md:pt-6 bg-white/60 rounded-lg p-6 shadow-sm">
        <flux:heading class="font-serif">{{ $heading ?? '' }}</flux:heading>
        <flux:subheading>{{ $subheading ?? '' }}</flux:subheading>

        <div class="mt-5 w-full max-w-lg">
            {{ $slot }}
        </div>
    </div>
</div>