<div class="p-6 max-w-6xl mx-auto">
    <div class="mb-8 flex items-start justify-between flex-wrap gap-4">
        <div>
            <p class="text-xs text-steel font-mono uppercase tracking-widest font-medium">{{ now()->format('l, F j, Y') }}</p>
            <flux:heading size="xl" class="font-serif text-ink">Welcome back, {{ auth()->user()->name }}</flux:heading>
            <p class="text-steel text-sm">{{ ucfirst(auth()->user()->role) }} &middot; Sangguniang Bayan of Allacapan</p>
        </div>
    </div>

    {{-- Stat cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white border border-gray-200 rounded-xl p-5 flex items-start gap-4 hover:shadow-md transition-shadow">
            <div class="shrink-0 size-10 rounded-lg flex items-center justify-center" style="background-color: rgba(27,67,50,0.1);">
                <flux:icon name="document-duplicate" class="size-5 text-ledger-green" />
            </div>
            <div>
                <p class="text-xs text-steel font-medium mb-1">Total Documents</p>
                <p class="text-3xl font-serif font-semibold text-ledger-green leading-none">{{ $totalDocuments }}</p>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-5 flex items-start gap-4 hover:shadow-md transition-shadow">
            <div class="shrink-0 size-10 rounded-lg flex items-center justify-center" style="background-color: rgba(27,67,50,0.1);">
                <flux:icon name="scale" class="size-5 text-ledger-green" />
            </div>
            <div>
                <p class="text-xs text-steel font-medium mb-1">Ordinances</p>
                <p class="text-3xl font-serif font-semibold text-ledger-green leading-none">{{ $totalOrdinances }}</p>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-5 flex items-start gap-4 hover:shadow-md transition-shadow">
            <div class="shrink-0 size-10 rounded-lg flex items-center justify-center" style="background-color: rgba(27,67,50,0.1);">
                <flux:icon name="clipboard-document-check" class="size-5 text-ledger-green" />
            </div>
            <div>
                <p class="text-xs text-steel font-medium mb-1">Resolutions</p>
                <p class="text-3xl font-serif font-semibold text-ledger-green leading-none">{{ $totalResolutions }}</p>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-5 flex items-start gap-4 hover:shadow-md transition-shadow">
            <div class="shrink-0 size-10 rounded-lg flex items-center justify-center" style="background-color: rgba(139,38,53,0.1);">
                <flux:icon name="exclamation-triangle" class="size-5 text-seal-red" />
            </div>
            <div>
                <p class="text-xs text-steel font-medium mb-1">Pending Amendments</p>
                <p class="text-3xl font-serif font-semibold text-seal-red leading-none">{{ $pendingAmendments }}</p>
            </div>
        </div>
    </div>

    {{-- Documents by Status --}}
    <div class="mb-8">
        <p class="text-xs uppercase tracking-widest text-ink font-mono font-bold mb-3 border-b-2 border-seal-gold inline-block pb-1">
            Documents by Status
        </p>
        @if ($statusBreakdown->isEmpty())
            <p class="text-sm text-steel">No documents yet.</p>
        @else
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                @foreach ($statusBreakdown as $status => $count)
                    <div class="status-widget">
                        <div class="status-widget-bar" style="background-color: {{ \App\Support\StatusColors::accent($status) }};"></div>
                        <div class="status-widget-body">
                            <p class="text-2xl font-serif font-semibold text-ink leading-none">{{ $count }}</p>
                            <x-status-pill :status="$status" class="mt-2" />
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Two-column content --}}
    <div class="grid lg:grid-cols-2 gap-6">
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <p class="text-xs uppercase tracking-widest text-ink font-mono font-bold">
                    Recently Filed
                </p>
                <flux:button href="{{ route('documents.index') }}" size="sm" variant="ghost">View All</flux:button>
            </div>
            <div class="px-5">
                @forelse ($recentDocuments as $doc)
                    <a href="{{ route('documents.show', $doc->document_id) }}" wire:navigate class="flex items-start gap-3 py-3 border-b border-gray-100 last:border-0 hover:bg-gray-50 -mx-2 px-2 rounded transition-colors">
                        <span class="shrink-0 mt-1 size-2 rounded-full" style="background-color: {{ $doc->document_type === 'Ordinance' ? '#1B4332' : '#8A6D1A' }};"></span>
                        <div class="min-w-0">
                            <div class="font-medium text-ink font-serif text-sm leading-snug">{{ $doc->title }}</div>
                            <div class="text-steel text-xs mt-0.5">
                                <span class="font-mono">{{ $doc->series_number }}</span> &middot; {{ $doc->date_filed->format('M d, Y') }} &middot; by {{ $doc->encoder->name }}
                            </div>
                        </div>
                    </a>
                @empty
                    <p class="text-sm text-steel py-4">No documents filed yet.</p>
                @endforelse
            </div>
            <div class="pb-2"></div>
        </div>

        @if (auth()->user()->isAdmin())
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                    <p class="text-xs uppercase tracking-widest text-ink font-mono font-bold">
                        Recent Activity
                    </p>
                    <flux:button href="{{ route('audit-logs.index') }}" size="sm" variant="ghost">View All</flux:button>
                </div>
                <div class="px-5">
                    @forelse ($recentActivity as $log)
                        <div class="py-3 border-b border-gray-100 last:border-0 text-sm">
                            <span class="text-steel text-xs font-mono">{{ $log->logged_at->diffForHumans() }}</span>
                            <div class="mt-0.5"><strong class="text-ink">{{ $log->user->name }}</strong> <span class="text-steel">{{ $log->action }}</span></div>
                        </div>
                    @empty
                        <p class="text-sm text-steel py-4">No activity recorded yet.</p>
                    @endforelse
                </div>
                <div class="pb-2"></div>
            </div>
        @endif
    </div>
</div>