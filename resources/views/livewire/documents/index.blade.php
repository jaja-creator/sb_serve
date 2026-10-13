<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <flux:heading size="lg" class="font-serif text-ink">Legislative Documents</flux:heading>
            <p class="text-xs text-steel font-mono uppercase tracking-widest mt-1">Registry &middot; {{ $documents->total() }} record(s) on file</p>
        </div>

        <div class="flex items-center gap-2">
            @if (auth()->user()->isAdmin())
                <flux:button href="{{ route('documents.trash') }}" variant="ghost" icon="trash" class="!text-ink">
                    Trash
                </flux:button>
            @endif

            @if (auth()->user()->isAdmin() || auth()->user()->isEncoder())
                <flux:button href="{{ route('documents.create') }}" variant="primary" icon="plus">
                    Encode Document
                </flux:button>
            @endif
        </div>
    </div>

    @if (session('success'))
        <flux:callout variant="success" class="mb-4">
            {{ session('success') }}
        </flux:callout>
    @endif

    <div class="border border-gray-300 rounded-lg p-5 mb-6 flex flex-wrap gap-4">
        <flux:input wire:model.live.debounce.400ms="search" placeholder="Search by title, series number, or sponsor..." icon="magnifying-glass" class="flex-1 min-w-[200px] !text-ink [&_input]:border [&_input]:border-gray-300 [&_input]:px-3 [&_input]:py-2" />

        <flux:select wire:model.live="typeFilter" placeholder="All Types" class="w-full sm:w-40 !text-ink [&_button]:border [&_button]:border-gray-300 [&_button]:px-3 [&_button]:py-2">
            <flux:select.option value="">All Types</flux:select.option>
            <flux:select.option value="Ordinance">Ordinance</flux:select.option>
            <flux:select.option value="Resolution">Resolution</flux:select.option>
        </flux:select>

        <flux:select wire:model.live="statusFilter" placeholder="All Statuses" class="w-full sm:w-48 !text-ink [&_button]:border [&_button]:border-gray-300 [&_button]:px-3 [&_button]:py-2">
            <flux:select.option value="">All Statuses</flux:select.option>
            @foreach (\App\Models\Document::STATUSES as $status)
                <flux:select.option value="{{ $status }}">{{ $status }}</flux:select.option>
            @endforeach
        </flux:select>

        <flux:select wire:model.live="sortField" placeholder="Sort by" class="w-full sm:w-44 !text-ink [&_button]:border [&_button]:border-gray-300 [&_button]:px-3 [&_button]:py-2">
            @foreach (\App\Livewire\Documents\Index::SORTABLE_FIELDS as $field => $label)
                <flux:select.option value="{{ $field }}">Sort: {{ $label }}</flux:select.option>
            @endforeach
        </flux:select>

        <flux:button
            wire:click="sortBy('{{ $sortField }}')"
            variant="ghost"
            icon="{{ $sortDirection === 'asc' ? 'bars-arrow-up' : 'bars-arrow-down' }}"
            class="!text-ink border border-gray-300"
        >
            {{ $sortDirection === 'asc' ? 'Ascending' : 'Descending' }}
        </flux:button>
    </div>

    <div class="border border-gray-300 rounded-lg overflow-hidden mb-6">
        <flux:table>
            <flux:table.columns>
                <flux:table.column class="!text-ink !font-semibold">Document</flux:table.column>
                <flux:table.column class="!text-ink !font-semibold w-44">Sponsor</flux:table.column>
                <flux:table.column class="!text-ink !font-semibold w-48">Status</flux:table.column>
                <flux:table.column class="!text-ink !font-semibold w-28">Date Filed</flux:table.column>
                <flux:table.column class="w-10"></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($documents as $document)
                    <flux:table.row wire:key="document-{{ $document->document_id }}" class="hover:bg-gray-50 transition-colors">
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

                        <flux:table.cell>
                            <flux:dropdown position="bottom" align="end">
                                <flux:button icon="ellipsis-horizontal" variant="ghost" size="sm" aria-label="Row actions" class="!text-ink" />

                                <flux:menu>
                                    <flux:menu.item icon="eye" href="{{ route('documents.show', $document->document_id) }}" wire:navigate>
                                        View
                                    </flux:menu.item>

                                    @if (auth()->user()->isAdmin() || auth()->user()->isEncoder())
                                        <flux:menu.item icon="pencil" href="{{ route('documents.edit', $document->document_id) }}" wire:navigate>
                                            Edit
                                        </flux:menu.item>
                                    @endif

                                    @if (auth()->user()->canDelete())
                                        <flux:menu.separator />
                                        <flux:menu.item
                                            icon="trash"
                                            variant="danger"
                                            wire:click="deleteDocument({{ $document->document_id }})"
                                            wire:confirm="Are you sure you want to delete this document?"
                                        >
                                            Delete
                                        </flux:menu.item>
                                    @endif
                                </flux:menu>
                            </flux:dropdown>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="5" class="text-center text-steel py-10">
                            No documents found in the registry.
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