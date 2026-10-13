<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen antialiased bg-parchment flex flex-col">

        {{-- Full-bleed hero photo header, matching login/dashboard --}}
        <header class="relative h-56 md:h-64 w-full overflow-hidden shrink-0">
            <img
                src="{{ asset('images/allacapan-hall.jpg') }}"
                alt="Allacapan Municipal Hall"
                class="absolute inset-0 w-full h-full object-cover"
            >
            <div class="absolute inset-0" style="background: linear-gradient(to bottom, rgba(11,36,71,0.25) 0%, rgba(7,26,51,0.85) 100%);"></div>

            <div class="relative z-20 h-full flex flex-col">
                {{-- Top row: seal + staff login --}}
                <div class="flex items-center justify-between px-6 pt-4">
                    <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-3">
                        <x-app-seal :size="48" class="drop-shadow-lg" />
                    </a>

                    <a href="{{ route('login') }}" wire:navigate class="px-4 py-2 rounded-md text-sm font-semibold text-white transition-colors" style="background-color: #C9A227; color: #211D18;">
                        Staff Login
                    </a>
                </div>

                {{-- Title block, bottom-aligned --}}
                <div class="flex-1 flex flex-col items-center justify-end pb-6 text-center px-6">
                    <span class="text-[10px] uppercase tracking-[0.3em] font-mono mb-1" style="color: rgba(245,240,230,0.9); text-shadow: 0 1px 3px rgba(0,0,0,0.6);">
                        Republic of the Philippines
                    </span>
                    <span class="text-parchment text-4xl leading-none tracking-wide" style="font-family: 'UnifrakturCook', 'Source Serif 4', serif; text-shadow: 0 2px 6px rgba(0,0,0,0.5);">SB-Serve</span>
                    <span class="text-parchment text-sm mt-1" style="font-family: 'Source Serif 4', serif; text-shadow: 0 1px 3px rgba(0,0,0,0.6);">
                        Sangguniang Bayan ng Allacapan, Cagayan
                    </span>
                    <span class="text-[10px] uppercase tracking-widest font-mono mt-1" style="color: rgba(245,240,230,0.9); text-shadow: 0 1px 3px rgba(0,0,0,0.6);">
                        Public Legislative Records Portal
                    </span>
                </div>
            </div>
        </header>

        <div class="h-1 bg-seal-gold shrink-0"></div>

        {{-- Public nav, below the hero --}}
        <nav class="bg-white border-b border-gray-300 shrink-0 shadow-sm">
            <div class="max-w-6xl mx-auto px-4 flex flex-wrap items-center justify-center gap-1">
                <a href="{{ route('home') }}" wire:navigate class="px-4 py-3 text-sm font-medium {{ request()->routeIs('home') ? 'text-ledger-green' : 'text-steel hover:text-ink' }}">
                    Home
                </a>
                <a href="{{ route('public.documents.index') }}" wire:navigate class="px-4 py-3 text-sm font-medium {{ request()->routeIs('public.documents.*') ? 'text-ledger-green' : 'text-steel hover:text-ink' }}">
                    Browse Ordinances &amp; Resolutions
                </a>
                <a href="{{ route('public.propose') }}" wire:navigate class="px-4 py-3 text-sm font-medium {{ request()->routeIs('public.propose') ? 'text-ledger-green' : 'text-steel hover:text-ink' }}">
                    Propose a Measure
                </a>
            </div>
        </nav>

        <main class="flex-1 max-w-6xl w-full mx-auto">
            {{ $slot }}
        </main>

        <footer class="border-t border-gray-200 py-6 mt-12 shrink-0">
            <div class="max-w-6xl mx-auto px-6 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-steel">
                <p>
                    Sangguniang Bayan of Allacapan &middot; Public Legislative Records Portal
                    &middot; <a href="mailto:sbo.allacapan@gmail.com" class="text-ledger-green hover:underline font-medium">sbo.allacapan@gmail.com</a>
                </p>
                <a href="https://www.facebook.com/profile.php?id=61586592647177" target="_blank" rel="noopener" class="text-ledger-green hover:underline font-medium">
    Follow us on Facebook
</a>
            </div>
        </footer>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>