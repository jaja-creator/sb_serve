<div class="p-6 max-w-5xl mx-auto">
    <div class="flex items-center justify-between mb-2 print:hidden">
        <div>
            <flux:heading size="lg" class="font-serif text-ink">Alphabetical Topic Index</flux:heading>
            <p class="text-xs text-steel font-mono uppercase tracking-widest mt-1">Gazetteer of Legislative Subjects</p>
        </div>
        <flux:button onclick="window.print()" variant="ghost" icon="printer" class="!text-ink">
            Print
        </flux:button>
    </div>
    <p class="text-steel mb-6 print:hidden">
        Browse all legislative documents by topic. This page can be printed for offline reference during power or internet outages.
    </p>

    <div class="hidden print:block mb-6 border-b-2 border-ink pb-4">
        <p class="text-xs uppercase tracking-widest font-mono">Republic of the Philippines</p>
        <h1 class="text-xl font-serif font-bold">Sangguniang Bayan of Allacapan</h1>
        <p class="text-sm">Alphabetical Topic Index</p>
        <p class="text-xs text-steel font-mono mt-1">Printed {{ now()->format('F d, Y g:i A') }}</p>
    </div>

    <div class="flex gap-6 print:block">
        {{-- Letter nav rail --}}
        <div class="flex flex-col gap-0.5 shrink-0 print:hidden">
            @foreach (range('A', 'Z') as $letter)
                @if ($availableLetters->contains($letter))
                    <a href="#letter-{{ $letter }}" class="w-8 h-7 flex items-center justify-center text-xs font-mono font-semibold rounded text-ledger-green hover:bg-gray-100">{{ $letter }}</a>
                @else
                    <span class="w-8 h-7 flex items-center justify-center text-xs font-mono text-gray-300">{{ $letter }}</span>
                @endif
            @endforeach
        </div>

        {{-- Content --}}
        <div class="bg-white border border-gray-200 rounded-lg p-6 flex-1">
            @forelse ($groupedDocuments as $letter => $documents)
                <div id="letter-{{ $letter }}" class="mb-8 pb-6 border-b border-gray-100 last:border-0 last:mb-0 last:pb-0">
                    <div class="flex items-baseline gap-3 mb-3">
                        <span class="font-serif font-bold text-xl text-ledger-green">{{ $letter }}</span>
                        <span class="text-xs text-steel font-mono uppercase tracking-widest">{{ count($documents) }} {{ Str::plural('entry', count($documents)) }}</span>
                    </div>

                    <div>
                        @foreach ($documents as $document)
                            <a href="{{ route('documents.show', $document->document_id) }}" wire:navigate class="block py-3 border-b border-gray-100 last:border-0 text-sm hover:bg-gray-50 -mx-2 px-2 rounded print:hover:bg-transparent">
                                <div class="font-medium text-ink font-serif">{{ $document->subject_matter ?: $document->title }}</div>
                                <div class="text-steel text-xs mt-0.5">
                                    <span class="font-mono">{{ $document->series_number }}</span> &middot; {{ $document->title }} &middot; {{ $document->document_type }} &middot; {{ $document->status }}
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @empty
                <p class="text-sm text-steel">No documents available yet.</p>
            @endforelse
        </div>
    </div>
</div>