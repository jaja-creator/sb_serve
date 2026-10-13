<div class="p-6 max-w-4xl mx-auto">
    @if (session('success'))
        <flux:callout variant="success" class="mb-4">
            {{ session('success') }}
        </flux:callout>
    @endif

    <div class="flex items-center justify-between mb-6">
        <div>
            <p class="text-xs uppercase tracking-widest text-steel font-mono mb-1">Registry &middot; Deleted Records</p>
            <flux:heading size="xl" class="font-serif">Document Trash</flux:heading>
        </div>
        <flux:button href="{{ route('documents.index') }}" variant="ghost">← Back to Documents</flux:button>
    </div>

    <flux:input wire:model.live.debounce.400ms="search" placeholder="Search deleted documents..." class="mb-4" />

    <div class="bg-white/60 rounded-lg shadow-sm divide-y divide-steel/20">
        @forelse ($documents as $document)
            <div class="p-4 flex items-center justify-between gap-4">
                <div>
                    <div class="font-medium text-ink">{{ $document->title }}</div>
                    <div class="text-xs text-steel font-mono">
                        {{ $document->series_number }} &middot; Deleted {{ $document->deleted_at->format('M d, Y g:i A') }}
                    </div>
                </div>
                <flux:button
                    wire:click="restore({{ $document->document_id }})"
                    wire:loading.attr="disabled"
                    wire:target="restore({{ $document->document_id }})"
                    variant="primary"
                    size="sm"
                >
                    Restore
                </flux:button>
            </div>
        @empty
            <p class="p-6 text-sm text-steel text-center">No deleted documents.</p>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $documents->links() }}
    </div>
</div>