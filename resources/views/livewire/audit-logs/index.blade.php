<div class="p-6 max-w-5xl mx-auto">
    <div class="mb-6">
        <flux:heading size="lg" class="font-serif text-ink">Audit Logs</flux:heading>
        <p class="text-sm text-steel mt-1">System activity history</p>
    </div>

    <div class="border border-gray-300 rounded-lg p-5 mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
        <flux:input wire:model.live.debounce.400ms="search" placeholder="Search by action or user..." icon="magnifying-glass" class="!text-ink [&_input]:border [&_input]:border-gray-300 [&_input]:px-3 [&_input]:py-2" />
        <flux:input wire:model.live="dateFrom" type="date" label="From" class="!text-ink [&_input]:border [&_input]:border-gray-300 [&_input]:px-3 [&_input]:py-2" />
        <flux:input wire:model.live="dateTo" type="date" label="To" class="!text-ink [&_input]:border [&_input]:border-gray-300 [&_input]:px-3 [&_input]:py-2" />
    </div>

    <div class="border border-gray-300 rounded-lg divide-y divide-gray-100">
        @forelse ($logs as $log)
            <div class="p-4" wire:key="log-{{ $log->log_id }}">
                <div class="flex items-start justify-between gap-4">
                    <p class="text-sm text-ink">
                        <strong class="font-semibold">{{ $log->user->name }}</strong>
                        <span class="text-steel">{{ $log->action }}</span>
                    </p>
                    <span class="text-xs text-steel font-mono whitespace-nowrap shrink-0">
                        {{ $log->logged_at->format('M d, Y') }} &middot; {{ $log->logged_at->format('g:i A') }}
                    </span>
                </div>

                <div class="flex items-center gap-2 mt-2">
                    @if ($log->document)
                        <a href="{{ route('documents.show', $log->document->document_id) }}" wire:navigate class="text-xs font-mono font-medium text-ledger-green hover:underline bg-green-50 px-2 py-0.5 rounded">
                            {{ $log->document->series_number }}
                        </a>
                    @endif
                    <span class="text-xs text-steel font-mono bg-gray-50 px-2 py-0.5 rounded">{{ $log->ip_address }}</span>
                </div>
            </div>
        @empty
            <div class="p-10 text-center text-steel">
                No audit log entries found.
            </div>
        @endforelse
    </div>

    <div class="mt-6">{{ $logs->links() }}</div>
</div>