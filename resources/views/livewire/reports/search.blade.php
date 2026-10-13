<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <flux:heading size="lg" class="font-serif text-ink">Reports &amp; Search</flux:heading>
            <p class="text-sm text-steel mt-1">{{ $documents->total() }} record(s) matched</p>
        </div>

        @if (auth()->user()->isAdmin())
            <flux:button wire:click="exportCsv" variant="primary" icon="arrow-down-tray">
                Export CSV
            </flux:button>
        @endif
    </div>

    <div class="border border-gray-300 rounded-lg p-5 mb-6 space-y-4">
        <flux:input wire:model.live.debounce.400ms="search" placeholder="Search by title, series number, or subject..." icon="magnifying-glass" class="!text-ink [&_input]:border [&_input]:border-gray-300 [&_input]:px-3 [&_input]:py-2" />

        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <flux:select wire:model.live="typeFilter" placeholder="All Types" class="!text-ink [&_button]:border [&_button]:border-gray-300 [&_button]:px-3 [&_button]:py-2">
                <flux:select.option value="">All Types</flux:select.option>
                <flux:select.option value="Ordinance">Ordinance</flux:select.option>
                <flux:select.option value="Resolution">Resolution</flux:select.option>
            </flux:select>

            <flux:select wire:model.live="statusFilter" placeholder="All Statuses" class="!text-ink [&_button]:border [&_button]:border-gray-300 [&_button]:px-3 [&_button]:py-2">
                <flux:select.option value="">All Statuses</flux:select.option>
                @foreach (\App\Models\Document::STATUSES as $status)
                    <flux:select.option value="{{ $status }}">{{ $status }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:input wire:model.live.debounce.400ms="sponsorFilter" placeholder="Sponsor" class="!text-ink [&_input]:border [&_input]:border-gray-300 [&_input]:px-3 [&_input]:py-2" />

            <flux:input wire:model.live="dateFrom" type="date" placeholder="Date From" class="!text-ink [&_input]:border [&_input]:border-gray-300 [&_input]:px-3 [&_input]:py-2" />

            <flux:input wire:model.live="dateTo" type="date" placeholder="Date To" class="!text-ink [&_input]:border [&_input]:border-gray-300 [&_input]:px-3 [&_input]:py-2" />
        </div>

        <div class="flex justify-end">
            <button wire:click="resetFilters" class="text-sm font-medium text-steel hover:text-ink transition-colors">
                Reset Filters
            </button>
        </div>
    </div>

    <div class="border border-gray-300 rounded-lg overflow-hidden mb-6">
        <flux:table>
            <flux:table.columns>
                <flux:table.column class="!text-ink !font-semibold">Document</flux:table.column>
                <flux:table.column class="!text-ink !font-semibold w-44">Sponsor</flux:table.column>
                <flux:table.column class="!text-ink !font-semibold w-48">Status</flux:table.column>
                <flux:table.column class="!text-ink !font-semibold w-28">Date Filed</flux:table.column>
                <flux:table.column class="!text-ink !font-semibold w-36">Encoded By</flux:table.column>
                <flux:table.column class="w-10"></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($documents as $document)
                    <flux:table.row wire:key="report-document-{{ $document->document_id }}" class="hover:bg-gray-50 transition-colors">
                        <flux:table.cell>
                            <a href="{{ route('documents.show', $document->document_id) }}" wire:navigate class="block">
                                <div class="font-serif font-semibold text-ink leading-snug">{{ $document->title }}</div>
                                <div class="text-xs text-steel font-mono mt-0.5">{{ $document->document_type }} &middot; {{ $document->series_number }}</div>
                            </a>
                        </flux:table.cell>

                        <flux:table.cell class="text-sm !text-ink font-medium">{{ $document->sponsor }}</flux:table.cell>

                        <flux:table.cell>
                            <x-status-pill :status="$document->status" />
                        </flux:table.cell>

                        <flux:table.cell class="text-sm !text-ink font-mono whitespace-nowrap">
                            {{ $document->date_filed->format('M d, Y') }}
                        </flux:table.cell>

                        <flux:table.cell class="text-sm !text-ink">
                            {{ $document->encoder->name ?? '—' }}
                        </flux:table.cell>

                        <flux:table.cell>
                            <a href="{{ route('documents.show', $document->document_id) }}" wire:navigate class="inline-flex items-center justify-center w-8 h-8 rounded text-steel hover:text-ledger-green hover:bg-gray-50 transition-colors" aria-label="View document">
                                <flux:icon name="eye" class="size-4" />
                            </a>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="6" class="text-center text-steel py-10">
                            No documents matched your search criteria.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>

    <div class="mt-4">
        {{ $documents->links() }}
    </div>
</div>