
<div class="p-6 max-w-4xl mx-auto">
    {{-- Official Legislative Print Header (visible only on print) --}}
    <div class="hidden print:block text-center mb-6 pb-4 border-b-2 border-black space-y-1">
        <p class="text-[11px] uppercase tracking-widest font-serif">Republic of the Philippines</p>
        <p class="text-[11px] uppercase tracking-wider font-serif">Province of Cagayan</p>
        <p class="text-[11px] uppercase tracking-wider font-serif">{{ \App\Models\SystemSetting::get('municipality', 'Municipality of Allacapan') }}</p>
        <p class="text-sm font-bold uppercase tracking-widest font-serif pt-1">{{ \App\Models\SystemSetting::get('organization_name', 'Office of the Sangguniang Bayan') }}</p>
    </div>

    <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6 flex items-start justify-between gap-6 print:border-none print:p-0">
        <div>
            <p class="text-xs uppercase tracking-widest text-steel font-mono mb-1">{{ $document->document_type }} &middot; {{ $document->series_number }}</p>
            <flux:heading size="xl" class="font-serif text-ink leading-tight">{{ $document->title }}</flux:heading>
        </div>
        <div class="flex items-center gap-3 shrink-0 print:hidden">
            <flux:button onclick="window.print()" variant="ghost" size="sm" icon="printer" class="!text-ink">
                Print Copy
            </flux:button>
            <x-status-pill :status="$document->status" />
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-lg p-4 grid grid-cols-2 gap-4 mb-6 text-sm">
        <div><span class="text-steel">Sponsor:</span> <span class="text-ink">{{ $document->sponsor }}</span></div>
        <div><span class="text-steel">Date Filed:</span> <span class="text-ink">{{ $document->date_filed->format('M d, Y') }}</span></div>
        <div><span class="text-steel">Date Enacted:</span> <span class="text-ink">{{ $document->date_enacted?->format('M d, Y') ?? '—' }}</span></div>
        <div><span class="text-steel">Subject Matter:</span> <span class="text-ink">{{ $document->subject_matter ?? '—' }}</span></div>
        @if ($document->file_path)
            <div class="col-span-2">
                <a href="{{ asset('storage/' . $document->file_path) }}" target="_blank" class="text-ledger-green underline text-sm">View Scanned Copy</a>
            </div>
        @endif
    </div>

    @if ($document->ordinance || $document->resolution)
        @php $detail = $document->ordinance ?? $document->resolution; @endphp
        <div class="bg-white border border-gray-200 rounded-lg p-4 mb-6">
            <flux:heading size="md" class="font-serif text-ink mb-3">{{ $document->document_type }} Details</flux:heading>
            <div class="text-sm space-y-1">
                <div><span class="text-steel">Author:</span> <span class="text-ink">{{ $detail->author }}</span></div>
                <div><span class="text-steel">Stage:</span> <span class="text-ink">{{ $detail->stage }}</span></div>
                @if ($detail->purpose ?? null)
                    <div><span class="text-steel">Purpose:</span> <span class="text-ink">{{ $detail->purpose }}</span></div>
                @endif
                @if ($detail->implementing_agency)
                    <div><span class="text-steel">Implementing Agency:</span> <span class="text-ink">{{ $detail->implementing_agency }}</span></div>
                @endif
            </div>
        </div>
    @endif

    @if ($document->committeeReports->isNotEmpty())
        <div class="bg-white border border-gray-200 rounded-lg p-4 mb-6">
            <flux:heading size="md" class="font-serif text-ink mb-3">Committee Reports</flux:heading>
            @foreach ($document->committeeReports as $report)
                <div class="text-sm py-3 border-b border-gray-100 last:border-0">
                    <div class="font-medium text-ink">{{ $report->report_title }}</div>
                    <div class="text-steel">{{ $report->committee_name }} &middot; {{ $report->report_date->format('M d, Y') }}</div>
                    <flux:badge class="mt-1">{{ $report->recommendation }}</flux:badge>
                </div>
            @endforeach
        </div>
    @endif

    <div class="bg-white border border-gray-200 rounded-lg p-4">
        <flux:heading size="md" class="font-serif text-ink mb-3">Status History</flux:heading>
        @forelse ($document->statusHistory as $entry)
            <div class="text-sm py-2 border-b border-gray-100 last:border-0">
                <span class="text-steel font-mono">{{ $entry->changed_at->format('M d, Y') }}</span>
                — {{ $entry->old_status ?? 'None' }} → <strong class="text-ledger-green">{{ $entry->new_status }}</strong>
            </div>
        @empty
            <p class="text-sm text-steel">No status changes recorded.</p>
        @endforelse
    </div>

    <div class="print:hidden">
        <flux:button href="{{ route('public.documents.index') }}" wire:navigate variant="ghost" class="!text-ink mt-6">← Back to Search</flux:button>
    </div>
</div>