<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen antialiased bg-parchment flex flex-col">

        {{-- Full-bleed hero photo header --}}
        <header class="relative h-56 md:h-64 w-full overflow-hidden shrink-0">
            <img
                src="{{ asset('images/allacapan-hall.jpg') }}"
                alt="Allacapan Municipal Hall"
                class="absolute inset-0 w-full h-full object-cover"
            >
            <div class="absolute inset-0" style="background: linear-gradient(to bottom, rgba(11,36,71,0.25) 0%, rgba(7,26,51,0.85) 100%);"></div>

            <a href="{{ route('home') }}" wire:navigate class="relative h-full flex flex-col items-center justify-end pb-6 text-center px-6">
                <span class="text-parchment/75 text-[10px] uppercase tracking-[0.3em] font-mono mb-1">
                    Republic of the Philippines
                </span>
                <span class="text-parchment font-blackletter text-4xl leading-none tracking-wide">SB-Serve</span>
                <span class="text-parchment/90 font-serif text-sm mt-1">
                    Sangguniang Bayan ng Allacapan, Cagayan
                </span>
            </a>
        </header>

        <div class="h-1 bg-seal-gold shrink-0"></div>

        {{-- Content --}}
        <main class="flex-1 flex items-center justify-center p-6 md:p-10">
            <div class="w-full max-w-sm bg-white rounded-xl shadow-xl p-8 border-t-4 border-seal-gold">
                <div class="flex flex-col items-center text-center mb-4">
                    <x-app-seal :size="64" />
                </div>

                <div class="flex flex-col gap-6">
                    {{ $slot }}
                </div>
            </div>
        </main>

        <footer class="shrink-0 py-4 text-center border-t border-ink/10">
            <p class="text-ink/50 text-xs font-mono">
                Sangguniang Bayan of Allacapan &middot; Authorized Personnel Only
            </p>
        </footer>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>