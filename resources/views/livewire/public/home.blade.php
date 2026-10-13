<div>
    <div class="px-6 py-12 text-center bg-white border-b border-gray-200">
        <flux:heading size="xl" class="font-serif text-ink mb-2">Legislative Records of Allacapan</flux:heading>
        <p class="text-steel max-w-xl mx-auto mb-8">
            Search and track ordinances and resolutions passed by the Sangguniang Bayan, or propose a measure of your own.
        </p>

        <form wire:submit="goToSearch" class="max-w-xl mx-auto flex gap-2 mb-10">
            <div class="flex-1 relative">
                <flux:input
                    wire:model.live.debounce.250ms="search"
                    placeholder="Search by title, series number, or subject..."
                    icon="magnifying-glass"
                    class="!text-ink [&_input]:border [&_input]:border-gray-300 [&_input]:pl-10 [&_input]:pr-3 [&_input]:py-2.5 [&_input]:shadow-sm"
                />
                <div wire:loading wire:target="search" class="absolute right-3 top-1/2 -translate-y-1/2">
                    <flux:icon name="arrow-path" class="size-4 text-steel animate-spin" />
                </div>
            </div>
            <flux:button type="submit" variant="primary">Search</flux:button>
        </form>

        {{-- Public transparency stat strip --}}
        <div class="flex items-center justify-center gap-8 flex-wrap">
            <div>
                <p class="text-3xl font-serif font-semibold text-ledger-green leading-none">{{ $totalOrdinances }}</p>
                <p class="text-xs text-steel font-medium mt-1">Ordinances Filed</p>
            </div>
            <div class="w-px h-10 bg-gray-200"></div>
            <div>
                <p class="text-3xl font-serif font-semibold text-ledger-green leading-none">{{ $totalResolutions }}</p>
                <p class="text-xs text-steel font-medium mt-1">Resolutions Filed</p>
            </div>
            <div class="w-px h-10 bg-gray-200"></div>
            <div>
                <p class="text-3xl font-serif font-semibold text-ledger-green leading-none">{{ $totalEnacted }}</p>
                <p class="text-xs text-steel font-medium mt-1">Enacted Into Law</p>
            </div>
        </div>
    </div>

    <div class="px-6 py-12 grid lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <div class="flex items-center justify-between mb-1">
                <div>
                    <flux:heading size="md" class="font-serif text-ink">
                        {{ $search !== '' ? 'Search Results' : 'Recently Filed' }}
                    </flux:heading>
                    @if ($search !== '')
                        <p class="text-xs text-steel font-mono mt-0.5">
                            {{ $recent->count() }} result(s) for "{{ $search }}"
                            <button wire:click="$set('search', '')" class="!text-accent-content underline ml-1">clear</button>
                        </p>
                    @endif
                </div>
                <flux:button href="{{ route('public.documents.index') }}" wire:navigate variant="ghost" size="sm" class="!text-ink">
                    View All
                </flux:button>
            </div>

            <p class="text-xs text-steel mb-4">Click a document to view its full text and status history.</p>

            @if ($legend->isNotEmpty())
                <div class="mb-4 text-xs text-steel bg-gray-50 border border-gray-200 rounded-md px-4 py-3">
                    <p class="font-medium text-ink mb-1.5">What do these mean?</p>
                    <ul class="space-y-1">
                        @foreach ($legend as $status => $meaning)
                            <li>
                                <strong class="{{ $status === 'Enacted' ? 'text-ledger-green' : ($status === 'Vetoed' ? 'text-seal-red' : 'text-ink') }}">{{ $status }}</strong>
                                &mdash; {{ $meaning }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="border border-gray-300 rounded-lg divide-y divide-gray-100 bg-white shadow-sm overflow-hidden">
                @forelse ($recent as $document)
                    <a href="{{ route('public.documents.show', $document->document_id) }}" wire:navigate class="block p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between gap-4">
                            <div class="min-w-0">
                                <p class="font-serif font-semibold text-ink truncate">{{ $document->title }}</p>
                                <p class="text-xs text-steel font-mono mt-0.5">{{ $document->document_type }} &middot; {{ $document->series_number }} &middot; {{ $document->date_filed->format('M d, Y') }}</p>
                            </div>
                            <x-status-pill :status="$document->status" />
                        </div>
                    </a>
                @empty
                    <div class="p-10 text-center">
                        <flux:icon name="magnifying-glass" class="size-6 text-steel/40 mx-auto mb-2" />
                        <p class="text-steel text-sm">
                            {{ $search !== '' ? 'No documents match your search.' : 'No documents on file yet.' }}
                        </p>
                    </div>
                @endforelse
            </div>

            <div class="mt-8 border border-gray-300 rounded-lg p-6 bg-white shadow-sm">
                <flux:heading size="md" class="font-serif text-ink mb-1">Have a proposal for the Sanggunian?</flux:heading>
                <p class="text-sm text-steel mb-4">Residents can submit a proposed ordinance or resolution for review by the legislative body.</p>
                <flux:button href="{{ route('public.propose') }}" wire:navigate variant="primary">
                    Propose a Measure
                </flux:button>
            </div>
        </div>

        <div>
            <flux:heading size="md" class="font-serif text-ink mb-4">From Our Facebook Page</flux:heading>
            @include('partials.facebook-page-embed', ['height' => 600])
        </div>
    </div>
</div>