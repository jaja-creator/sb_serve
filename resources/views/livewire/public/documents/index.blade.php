<div class="p-6">
    <div class="mb-6">
        <flux:heading size="lg" class="font-serif text-ink">Browse Ordinances &amp; Resolutions</flux:heading>
        <p class="text-sm text-steel mt-1">{{ $documents->total() }} public record(s)</p>
    </div>

    <div class="border border-gray-300 rounded-lg p-5 mb-6 flex flex-wrap gap-4">
        <flux:input wire:model.live.debounce.400ms="search" placeholder="Search by title, series number, or subject..." icon="magnifying-glass" class="flex-1 min-w-[200px] !text-ink [&_input]:border [&_input]:border-gray-300 [&_input]:px-3 [&_input]:py-2" />
        <flux:select wire:model.live="typeFilter" placeholder="All Types" class="w-full sm:w-48 !text-ink [&_button]:border [&_button]:border-gray-300 [&_button]:px-3 [&_button]:py-2">
            <flux:select.option value="">All Types</flux:select.option>
            <flux:select.option value="Ordinance">Ordinance</flux:select.option>
            <flux:select.option value="Resolution">Resolution</flux:select.option>
        </flux:select>
    </div>

    <div class="border border-gray-300 rounded-lg divide-y divide-gray-100">
        @forelse ($documents as $document)
            <a href="{{ route('public.documents.show', $document->document_id) }}" wire:navigate class="block p-4 hover:bg-gray-50 transition-colors">
                <div class="flex items-center justify-between gap-4">
                    <div class="min-w-0">
                        <p class="font-serif font-semibold text-ink truncate">{{ $document->title }}</p>
                        <p class="text-xs text-steel font-mono mt-0.5">{{ $document->document_type }} &middot; {{ $document->series_number }} &middot; {{ $document->date_filed->format('M d, Y') }} &middot; {{ $document->sponsor }}</p>
                    </div>
                    <x-status-pill :status="$document->status" />
                </div>
            </a>
        @empty
            <p class="p-10 text-center text-steel">No documents matched your search.</p>
        @endforelse
    </div>

    <div class="mt-4">{{ $documents->links() }}</div>
</div>