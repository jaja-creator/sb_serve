<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen antialiased bg-parchment">

        {{-- Full-bleed hero photo header, matching the login page treatment --}}
        <header class="relative h-56 md:h-64 w-full overflow-hidden shrink-0 print:hidden">
            <img
                src="{{ asset('images/allacapan-hall.jpg') }}"
                alt="Allacapan Municipal Hall"
                class="absolute inset-0 w-full h-full object-cover"
            >
            <div class="absolute inset-0" style="background: linear-gradient(to bottom, rgba(11,36,71,0.25) 0%, rgba(7,26,51,0.85) 100%);"></div>

            <div class="relative z-20 h-full flex flex-col">
                {{-- Top row: seal + user menu --}}
                <div class="flex items-center justify-between px-6 pt-4">
                    <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-3">
                        <x-app-seal :size="48" class="drop-shadow-lg" />
                    </a>

                    <flux:dropdown position="bottom" align="end">
                        <button class="flex items-center gap-2 shrink-0 rounded-full p-0.5" style="background-color: rgba(7,26,51,0.55); backdrop-filter: blur(4px);">
                            <flux:avatar size="sm" :initials="auth()->user()->initials()" style="background-color: #C9A227; color: #211D18;" />
                        </button>

                        <flux:menu>
                            <flux:menu.radio.group>
                                <div class="p-0 text-sm font-normal">
                                    <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                        <flux:avatar :name="auth()->user()->name" :initials="auth()->user()->initials()" />
                                        <div class="grid flex-1 text-start text-sm leading-tight">
                                            <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                            <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                        </div>
                                    </div>
                                </div>
                            </flux:menu.radio.group>

                            <flux:menu.separator />

                            <flux:menu.radio.group>
                                <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                                    {{ __('Settings') }}
                                </flux:menu.item>
                            </flux:menu.radio.group>

                            <flux:menu.separator />

                            <form method="POST" action="{{ route('logout') }}" class="w-full">
                                @csrf
                                <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full cursor-pointer">
                                    {{ __('Log out') }}
                                </flux:menu.item>
                            </form>
                        </flux:menu>
                    </flux:dropdown>
                </div>

                {{-- Title block, bottom-aligned like the login hero --}}
                <div class="flex-1 flex flex-col items-center justify-end pb-6 text-center px-6">
                    <span class="text-[10px] uppercase tracking-[0.3em] font-mono mb-1" style="color: rgba(245,240,230,0.9); text-shadow: 0 1px 3px rgba(0,0,0,0.6);">
                        Republic of the Philippines
                    </span>
                    <span class="text-parchment text-4xl leading-none tracking-wide" style="font-family: 'UnifrakturCook', 'Source Serif 4', serif; text-shadow: 0 2px 6px rgba(0,0,0,0.5);">SB-Serve</span>
                    <span class="text-parchment text-sm mt-1" style="font-family: 'Source Serif 4', serif; text-shadow: 0 1px 3px rgba(0,0,0,0.6);">
                        Sangguniang Bayan ng Allacapan
                    </span>
                    <span class="text-[10px] uppercase tracking-widest font-mono mt-1" style="color: rgba(245,240,230,0.9); text-shadow: 0 1px 3px rgba(0,0,0,0.6);">
                        E-Document Management &amp; Legislative Tracking System
                    </span>
                </div>
            </div>
        </header>

        <div class="h-1 bg-seal-gold shrink-0 print:hidden"></div>

        {{-- Nav tabs, centered, below the hero --}}
@php
    $tabs = [
        ['route' => 'dashboard', 'match' => 'dashboard', 'icon' => 'home', 'label' => 'Dashboard'],
        ['route' => 'documents.index', 'match' => 'documents.*', 'icon' => 'folder-git-2', 'label' => 'Documents'],
        ['route' => 'reports.search', 'match' => 'reports.*', 'icon' => 'magnifying-glass', 'label' => 'Reports'],
        ['route' => 'topic-index', 'match' => 'topic-index', 'icon' => 'book-open', 'label' => 'Index'],
    ];
    if (auth()->user()->isAdmin()) {
        $tabs[] = ['route' => 'citizen-proposals.index', 'match' => 'citizen-proposals.*', 'icon' => 'chat-bubble-left-right', 'label' => 'Proposals'];
        $tabs[] = ['route' => 'users.index', 'match' => 'users.*', 'icon' => 'users', 'label' => 'Users'];
        $tabs[] = ['route' => 'audit-logs.index', 'match' => 'audit-logs.*', 'icon' => 'clipboard-document-list', 'label' => 'Logs'];
        $tabs[] = ['route' => 'backup.index', 'match' => 'backup.*', 'icon' => 'circle-stack', 'label' => 'Backup'];
        $tabs[] = ['route' => 'settings.system', 'match' => 'settings.system', 'icon' => 'cog-6-tooth', 'label' => 'Settings'];
    }
@endphp

        <nav class="bg-white border-b border-gray-200 shrink-0 shadow-sm print:hidden">
            <div class="max-w-7xl mx-auto px-4 flex flex-wrap items-center justify-center gap-1 overflow-x-auto">
                @foreach ($tabs as $tab)
                    @php $isCurrent = request()->routeIs($tab['match']); @endphp
                    <a href="{{ route($tab['route']) }}"
                        wire:navigate
                        class="relative flex items-center gap-2 px-4 py-3 text-sm font-medium rounded-md my-1.5 transition-colors whitespace-nowrap {{ $isCurrent ? 'text-ledger-green' : 'text-steel hover:text-ink hover:bg-gray-100' }}"
                        style="{{ $isCurrent ? 'background-color: rgba(201,162,39,0.15);' : '' }}"
                    >
                        <flux:icon :name="$tab['icon']" class="size-4 {{ $isCurrent ? 'text-seal-gold' : 'text-steel/70' }}" />
                        {{ $tab['label'] }}
                        @if ($isCurrent)
                            <span class="absolute left-4 right-4 -bottom-px h-0.5 bg-seal-gold rounded-full"></span>
                        @endif
                    </a>
                @endforeach
            </div>
        </nav>

        <main class="max-w-7xl mx-auto">
            {{ $slot }}
        </main>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>