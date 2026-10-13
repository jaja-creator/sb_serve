<div class="p-6 max-w-4xl mx-auto">
    {{-- Official Legislative Print Header (visible only on print) --}}
    <div class="hidden print:block text-center mb-6 pb-4 border-b-2 border-black space-y-1">
        <p class="text-[11px] uppercase tracking-widest font-serif">Republic of the Philippines</p>
        <p class="text-[11px] uppercase tracking-wider font-serif">Province of Cagayan</p>
        <p class="text-[11px] uppercase tracking-wider font-serif">{{ \App\Models\SystemSetting::get('municipality', 'Municipality of Allacapan') }}</p>
        <p class="text-sm font-bold uppercase tracking-widest font-serif pt-1">{{ \App\Models\SystemSetting::get('organization_name', 'Office of the Sangguniang Bayan') }}</p>
    </div>

    @if (session('success'))
        <flux:callout variant="success" class="mb-4 print:hidden">
            {{ session('success') }}
        </flux:callout>
    @endif

    @if (session('error'))
        <flux:callout variant="danger" class="mb-4 print:hidden">
            {{ session('error') }}
        </flux:callout>
    @endif

    {{-- Hero: Title + Seal --}}
    <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6 flex items-start justify-between gap-6 print:border-none print:p-0">
        <div>
            <p class="text-xs uppercase tracking-widest text-steel font-mono mb-1">{{ $document->document_type }} &middot; {{ $document->series_number }}</p>
            <flux:heading size="xl" class="font-serif text-ink leading-tight">{{ $document->title }}</flux:heading>
        </div>
        <div class="flex items-center gap-3 shrink-0 print:hidden">
            <flux:button onclick="window.print()" variant="ghost" size="sm" icon="printer" class="!text-ink">
                Print Official Copy
            </flux:button>
            <x-document-seal :status="$document->status" class="shrink-0" />
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-lg p-4 grid grid-cols-2 gap-4 mb-6 text-sm">
        <div><span class="text-steel">Sponsor:</span> <span class="text-ink">{{ $document->sponsor }}</span></div>
        <div><span class="text-steel">Date Filed:</span> <span class="text-ink">{{ $document->date_filed->format('M d, Y') }}</span></div>
        <div><span class="text-steel">Encoded by:</span> <span class="text-ink">{{ $document->encoder->name }}</span></div>
        <div><span class="text-steel">Subject Matter:</span> <span class="text-ink">{{ $document->subject_matter ?? '—' }}</span></div>
        @if ($document->document_type === 'Ordinance')
            <div><span class="text-steel">Date Approved by Mayor:</span> <span class="text-ink">{{ $document->date_approved_mayor?->format('M d, Y') ?? '—' }}</span></div>
        @endif
        <div class="col-span-2">
            <span class="text-steel">Scanned Copy:</span>
            @if ($document->file_path)
                <a href="{{ asset('storage/' . $document->file_path) }}" target="_blank" class="text-ledger-green underline">View / Download</a>
                <flux:button wire:click="verifyFileIntegrity" size="sm" variant="ghost" class="!text-ink">Verify Integrity</flux:button>
                @if ($document->file_hash)
                    <div class="text-xs text-steel font-mono mt-1">SHA-256: {{ Str::limit($document->file_hash, 24) }}...</div>
                @endif
            @else
                <span class="text-steel">Not uploaded</span>
            @endif
        </div>
    </div>

    {{-- Change Status --}}
    @if ($document->status === 'Enacted')
        <div class="mb-6 bg-white border border-green-200 rounded-lg p-4 flex items-center gap-2 print:hidden">
            <flux:icon name="check-badge" class="size-5 text-ledger-green shrink-0" />
            <p class="text-sm font-medium text-ink">This measure has been Enacted. Its status is final and cannot be changed.</p>
        </div>
    @elseif (auth()->user()->isAdmin() || auth()->user()->isEncoder())
        <div class="mb-6 bg-white border border-gray-200 rounded-lg p-4 print:hidden">
            @if (! $showStatusForm)
                <flux:button wire:click="$set('showStatusForm', true)" variant="ghost" size="sm" class="!text-ink">
                    Change Status
                </flux:button>
            @else
                <div class="space-y-3 max-w-md">
                    <flux:select wire:model.live="newStatus" label="New Status" class="[&_button]:!text-ink [&_button]:!border [&_button]:!border-gray-300">
                        @foreach ($document->availableStatuses() as $status)
                            <flux:select.option value="{{ $status }}">{{ $status }}</flux:select.option>
                        @endforeach
                    </flux:select>

                    @if ($newStatus === 'First Reading')
                        <flux:input wire:model="date_calendared" label="Date Calendared (First Reading)" type="date" class="[&_input]:!text-ink [&_input]:!border [&_input]:!border-gray-300" />
                    @elseif ($newStatus === 'For Committee Reporting')
                        <flux:input wire:model="date_referred_committee" label="Date Referred to Committee" type="date" class="[&_input]:!text-ink [&_input]:!border [&_input]:!border-gray-300" />
                    @elseif ($newStatus === 'Second Reading')
                        <flux:input wire:model="date_second_reading" label="Date Calendared (Second Reading)" type="date" class="[&_input]:!text-ink [&_input]:!border [&_input]:!border-gray-300" />
                    @elseif ($newStatus === 'Third Reading')
                        <flux:input wire:model="date_third_reading" label="Date Calendared (Third Reading)" type="date" class="[&_input]:!text-ink [&_input]:!border [&_input]:!border-gray-300" />
                    @elseif ($newStatus === 'Approved')
                        <flux:input wire:model="date_approved" label="Date Approved by the Sanggunian" type="date" class="[&_input]:!text-ink [&_input]:!border [&_input]:!border-gray-300" />
                    @elseif ($newStatus === 'Enacted')
                        <flux:input wire:model="date_enacted" label="Date Enacted/Adopted by SB" type="date" class="[&_input]:!text-ink [&_input]:!border [&_input]:!border-gray-300" />
                        <flux:select wire:model="passage_mode" label="Passage Mode" class="[&_button]:!text-ink [&_button]:!border [&_button]:!border-gray-300">
                            @foreach (\App\Models\Document::PASSAGE_MODES as $value => $label)
                                <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                            @endforeach
                        </flux:select>
                    @elseif ($newStatus === 'Vetoed')
                        <flux:input wire:model="date_vetoed" label="Date Vetoed" type="date" class="[&_input]:!text-ink [&_input]:!border [&_input]:!border-gray-300" />
                    @elseif ($newStatus === 'Returned')
                        <flux:input wire:model="date_returned" label="Date Returned" type="date" class="[&_input]:!text-ink [&_input]:!border [&_input]:!border-gray-300" />
                    @endif

                    <flux:textarea wire:model="statusRemarks" label="Remarks (optional)" class="[&_textarea]:!text-ink [&_textarea]:!border [&_textarea]:!border-gray-300" />

                    <div class="flex gap-2">
                        @if (auth()->user()->isAdmin() || (auth()->user()->isEncoder() && ! $document->isLocked()))
                            <flux:button wire:click="changeStatus" variant="primary" size="sm" wire:loading.attr="disabled" wire:target="changeStatus">
                                <span wire:loading.remove wire:target="changeStatus">Confirm</span>
                                <span wire:loading wire:target="changeStatus">Updating...</span>
                            </flux:button>
                            <flux:button wire:click="$set('showStatusForm', false)" variant="ghost" size="sm" class="!text-ink">Cancel</flux:button>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    @endif

    {{-- Ordinance / Resolution Details --}}
    <div class="mb-6 bg-white border border-gray-200 rounded-lg p-4">
        <flux:heading size="md" class="mb-3 font-serif text-ink">
            {{ $document->document_type }} Details
        </flux:heading>

        @if ($document->document_type === 'Ordinance')
            @if ($document->ordinance)
                <div class="text-sm space-y-1">
                    <div><span class="text-steel">Stage:</span> <span class="text-ink">{{ $document->ordinance->stage }}</span></div>
                    <div><span class="text-steel">Version:</span> <span class="font-mono text-ink">{{ $document->ordinance->version_number }}</span></div>
                    <div><span class="text-steel">Author:</span> <span class="text-ink">{{ $document->ordinance->author }}</span></div>
                    <div><span class="text-steel">Committee Referred:</span> <span class="text-ink">{{ $document->ordinance->committee_referred ?? '—' }}</span></div>
                    <div><span class="text-steel">Date Prepared:</span> <span class="text-ink">{{ $document->ordinance->date_prepared->format('M d, Y') }}</span></div>
                </div>
            @elseif (auth()->user()->isAdmin() || auth()->user()->isEncoder())
                @if (! $showStageForm)
                    <flux:button wire:click="$set('showStageForm', true)" variant="primary" size="sm">
                        Add Ordinance Details
                    </flux:button>
                @else
                    <form wire:submit="saveOrdinance" class="space-y-3 max-w-md">
                        <flux:select wire:model="stage" label="Stage" class="[&_button]:!text-ink [&_button]:!border [&_button]:!border-gray-300">
                            <flux:select.option value="Draft">Draft</flux:select.option>
                            <flux:select.option value="Committee Review">Committee Review</flux:select.option>
                            <flux:select.option value="Final">Final</flux:select.option>
                        </flux:select>
                        <flux:input wire:model="version_number" label="Version Number" class="[&_input]:!text-ink [&_input]:!border [&_input]:!border-gray-300" />
                        <flux:input wire:model="author" label="Author" class="[&_input]:!text-ink [&_input]:!border [&_input]:!border-gray-300" />
                        <flux:input wire:model="committee_referred" label="Committee Referred" class="[&_input]:!text-ink [&_input]:!border [&_input]:!border-gray-300" />
                        <flux:input wire:model="date_prepared" label="Date Prepared" type="date" class="[&_input]:!text-ink [&_input]:!border [&_input]:!border-gray-300" />
                        <flux:input wire:model="implementing_agency" label="Implementing Agency" class="[&_input]:!text-ink [&_input]:!border [&_input]:!border-gray-300" />
                        <flux:input wire:model="target_beneficiaries" label="Target Beneficiaries" class="[&_input]:!text-ink [&_input]:!border [&_input]:!border-gray-300" />
                        <flux:textarea wire:model="remarks" label="Remarks" class="[&_textarea]:!text-ink [&_textarea]:!border [&_textarea]:!border-gray-300" />
                        <div class="flex gap-2">
                            <flux:button type="submit" variant="primary" size="sm">Save</flux:button>
                            <flux:button wire:click="$set('showStageForm', false)" variant="ghost" size="sm" class="!text-ink">Cancel</flux:button>
                        </div>
                    </form>
                @endif
            @endif
        @elseif ($document->document_type === 'Resolution')
            @if ($document->resolution)
                <div class="text-sm space-y-1">
                    <div><span class="text-steel">Stage:</span> <span class="text-ink">{{ $document->resolution->stage }}</span></div>
                    <div><span class="text-steel">Version:</span> <span class="font-mono text-ink">{{ $document->resolution->version_number }}</span></div>
                    <div><span class="text-steel">Author:</span> <span class="text-ink">{{ $document->resolution->author }}</span></div>
                    <div><span class="text-steel">Purpose:</span> <span class="text-ink">{{ $document->resolution->purpose ?? '—' }}</span></div>
                    <div><span class="text-steel">Date Prepared:</span> <span class="text-ink">{{ $document->resolution->date_prepared->format('M d, Y') }}</span></div>
                </div>
            @elseif (auth()->user()->isAdmin() || auth()->user()->isEncoder())
                @if (! $showStageForm)
                    <flux:button wire:click="$set('showStageForm', true)" variant="primary" size="sm">
                        Add Resolution Details
                    </flux:button>
                @else
                    <form wire:submit="saveResolution" class="space-y-3 max-w-md">
                        <flux:select wire:model="stage" label="Stage" class="[&_button]:!text-ink [&_button]:!border [&_button]:!border-gray-300">
                            <flux:select.option value="Draft">Draft</flux:select.option>
                            <flux:select.option value="Committee Review">Committee Review</flux:select.option>
                            <flux:select.option value="Final">Final</flux:select.option>
                        </flux:select>
                        <flux:input wire:model="version_number" label="Version Number" class="[&_input]:!text-ink [&_input]:!border [&_input]:!border-gray-300" />
                        <flux:input wire:model="author" label="Author" class="[&_input]:!text-ink [&_input]:!border [&_input]:!border-gray-300" />
                        <flux:input wire:model="purpose" label="Purpose" class="[&_input]:!text-ink [&_input]:!border [&_input]:!border-gray-300" />
                        <flux:input wire:model="date_prepared" label="Date Prepared" type="date" class="[&_input]:!text-ink [&_input]:!border [&_input]:!border-gray-300" />
                        <flux:input wire:model="implementing_agency" label="Implementing Agency" class="[&_input]:!text-ink [&_input]:!border [&_input]:!border-gray-300" />
                        <flux:input wire:model="target_beneficiaries" label="Target Beneficiaries" class="[&_input]:!text-ink [&_input]:!border [&_input]:!border-gray-300" />
                        <flux:textarea wire:model="remarks" label="Remarks" class="[&_textarea]:!text-ink [&_textarea]:!border [&_textarea]:!border-gray-300" />
                        <div class="flex gap-2">
                            <flux:button type="submit" variant="primary" size="sm">Save</flux:button>
                            <flux:button wire:click="$set('showStageForm', false)" variant="ghost" size="sm" class="!text-ink">Cancel</flux:button>
                        </div>
                    </form>
                @endif
            @endif
        @endif
    </div>

    {{-- Committee Reports --}}
    <div class="mb-6 bg-white border border-gray-200 rounded-lg p-4">
        <div class="flex items-center justify-between mb-3">
            <flux:heading size="md" class="font-serif text-ink">Committee Reports</flux:heading>
            @if (auth()->user()->isAdmin() || auth()->user()->isEncoder())
                @if (! $showReportForm)
                    <flux:button wire:click="$set('showReportForm', true)" variant="primary" size="sm">
                        Add Committee Report
                    </flux:button>
                @endif
            @endif
        </div>

        @if ($showReportForm)
            <form wire:submit="saveCommitteeReport" class="space-y-3 max-w-md mb-4">
                <flux:select wire:model="committee_name" label="Committee Name" class="[&_button]:!text-ink [&_button]:!border [&_button]:!border-gray-300">
    <flux:select.option value="">Select a committee...</flux:select.option>
    @foreach (\App\Livewire\Documents\Show::STANDING_COMMITTEES as $committee)
        <flux:select.option value="{{ $committee }}">{{ $committee }}</flux:select.option>
    @endforeach
</flux:select>
                <flux:input wire:model="report_title" label="Report Title" class="[&_input]:!text-ink [&_input]:!border [&_input]:!border-gray-300" />
                <flux:input wire:model="report_date" label="Report Date" type="date" class="[&_input]:!text-ink [&_input]:!border [&_input]:!border-gray-300" />
                <flux:select wire:model="recommendation" label="Recommendation" class="[&_button]:!text-ink [&_button]:!border [&_button]:!border-gray-300">
                    @foreach (\App\Livewire\Documents\Show::RECOMMENDATIONS as $option)
                        <flux:select.option value="{{ $option }}">{{ $option }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:textarea wire:model="findings" label="Findings" class="[&_textarea]:!text-ink [&_textarea]:!border [&_textarea]:!border-gray-300" />
                <div class="flex gap-2">
                    <flux:button type="submit" variant="primary" size="sm">Save Report</flux:button>
                    <flux:button wire:click="$set('showReportForm', false)" variant="ghost" size="sm" class="!text-ink">Cancel</flux:button>
                </div>
            </form>
        @endif

        @forelse ($document->committeeReports as $report)
            <div class="text-sm py-3 border-b border-gray-100 last:border-0">
                <div class="font-medium text-ink">{{ $report->report_title }}</div>
                <div class="text-steel">{{ $report->committee_name }} &middot; {{ $report->report_date->format('M d, Y') }}</div>
                <div><flux:badge>{{ $report->recommendation }}</flux:badge></div>
                @if ($report->findings)
                    <div class="text-ink mt-1">{{ $report->findings }}</div>
                @endif
                <div class="text-steel text-xs mt-1">Prepared by {{ $report->preparer->name }}</div>
            </div>
        @empty
            <p class="text-sm text-steel">No committee reports yet.</p>
        @endforelse
    </div>

    {{-- Amendments --}}
    <div class="mb-6 bg-white border border-gray-200 rounded-lg p-4">
        <div class="flex items-center justify-between mb-3">
            <flux:heading size="md" class="font-serif text-ink">Amendments</flux:heading>
            @if (auth()->user()->isAdmin())
                @if (! $showAmendmentForm)
                    <flux:button wire:click="$set('showAmendmentForm', true)" variant="primary" size="sm">
                        Submit Amendment
                    </flux:button>
                @endif
            @endif
        </div>

        @if ($showAmendmentForm)
            <form wire:submit="saveAmendment" class="space-y-3 max-w-md mb-4">
                <flux:input wire:model="section_amended" label="Section Amended" class="[&_input]:!text-ink [&_input]:!border [&_input]:!border-gray-300" />
                <flux:textarea wire:model="amendment_description" label="Amendment Description" class="[&_textarea]:!text-ink [&_textarea]:!border [&_textarea]:!border-gray-300" />
                <flux:input wire:model="amendment_date" label="Amendment Date" type="date" class="[&_input]:!text-ink [&_input]:!border [&_input]:!border-gray-300" />
                <div class="flex gap-2">
                    <flux:button type="submit" variant="primary" size="sm">Submit</flux:button>
                    <flux:button wire:click="$set('showAmendmentForm', false)" variant="ghost" size="sm" class="!text-ink">Cancel</flux:button>
                </div>
            </form>
        @endif

        @forelse ($document->amendments as $amendment)
            <div class="text-sm py-3 border-b border-gray-100 last:border-0">
                <div class="flex items-center gap-2">
                    <span class="font-medium text-ink">Section: {{ $amendment->section_amended }}</span>
                    <flux:badge>{{ $amendment->amendment_status }}</flux:badge>
                </div>
                <div class="text-ink mt-1">{{ $amendment->amendment_description }}</div>
                <div class="text-steel text-xs mt-1">
                    Submitted by {{ $amendment->submitter->name }} on {{ $amendment->amendment_date->format('M d, Y') }}
                    @if ($amendment->reviewer)
                        &middot; Reviewed by {{ $amendment->reviewer->name }}
                    @endif
                </div>

                @if (auth()->user()->isAdmin() && $amendment->amendment_status === 'Pending')
                    <div class="flex gap-2 mt-2">
                        <flux:button
                            wire:click="reviewAmendment({{ $amendment->amendment_id }}, 'Approved')"
                            wire:loading.attr="disabled"
                            wire:target="reviewAmendment({{ $amendment->amendment_id }}, 'Approved')"
                            variant="primary"
                            size="sm"
                        >
                            Approve
                        </flux:button>
                        <flux:button
                            wire:click="reviewAmendment({{ $amendment->amendment_id }}, 'Rejected')"
                            wire:loading.attr="disabled"
                            wire:target="reviewAmendment({{ $amendment->amendment_id }}, 'Rejected')"
                            variant="danger"
                            size="sm"
                        >
                            Reject
                        </flux:button>
                    </div>
                @endif
            </div>
        @empty
            <p class="text-sm text-steel">No amendments submitted yet.</p>
        @endforelse
    </div>

    {{-- Document Links --}}
    <div class="mb-6 bg-white border border-gray-200 rounded-lg p-4">
        <div class="flex items-center justify-between mb-3">
            <flux:heading size="md" class="font-serif text-ink">Related References</flux:heading>
            @if (auth()->user()->isAdmin() || auth()->user()->isEncoder())
                @if (! $showLinkForm)
                    <flux:button wire:click="$set('showLinkForm', true)" variant="primary" size="sm">
                        Add Reference
                    </flux:button>
                @endif
            @endif
        </div>

        @if ($showLinkForm)
            <form wire:submit="saveLink" class="space-y-3 max-w-md mb-4">
                <flux:select wire:model="link_type" label="Reference Type" class="[&_button]:!text-ink [&_button]:!border [&_button]:!border-gray-300">
                    @foreach (\App\Livewire\Documents\Show::LINK_TYPES as $type)
                        <flux:select.option value="{{ $type }}">{{ $type }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:input wire:model="reference_title" label="Reference Title" class="[&_input]:!text-ink [&_input]:!border [&_input]:!border-gray-300" />
                <flux:input wire:model="reference_number" label="Reference Number (optional)" class="[&_input]:!text-ink [&_input]:!border [&_input]:!border-gray-300" />

                <flux:input wire:model="source_url" label="Source URL (optional)" placeholder="https://..." class="[&_input]:!text-ink [&_input]:!border [&_input]:!border-gray-300" />

                <div class="text-center text-sm text-steel">— or —</div>

                <flux:input type="file" wire:model="referenceFile" label="Upload File (PDF, Word, or Image, optional)" class="[&_input]:!text-ink [&_input]:!border [&_input]:!border-gray-300" />
                @if ($referenceFile)
                    <p class="text-xs text-steel">Selected: {{ $referenceFile->getClientOriginalName() }}</p>
                @endif
                <div wire:loading wire:target="referenceFile" class="text-xs text-steel">Uploading...</div>

                <div class="flex gap-2">
                    <flux:button type="submit" variant="primary" size="sm">Save</flux:button>
                    <flux:button wire:click="$set('showLinkForm', false)" variant="ghost" size="sm" class="!text-ink">Cancel</flux:button>
                </div>
            </form>
        @endif

        @forelse ($document->links as $link)
            <div class="text-sm py-2 border-b border-gray-100 last:border-0">
                <flux:badge>{{ $link->link_type }}</flux:badge>
                <span class="font-medium text-ink">{{ $link->reference_title }}</span>
                @if ($link->reference_number)
                    <span class="text-steel font-mono">({{ $link->reference_number }})</span>
                @endif
                @if ($link->source_url)
                    <a href="{{ $link->source_url }}" target="_blank" class="text-ledger-green underline text-xs ml-1">visit link</a>
                @endif
                @if ($link->file_path)
                    <a href="{{ asset('storage/' . $link->file_path) }}" target="_blank" class="text-ledger-green underline text-xs ml-1">view file</a>
                @endif
                <div class="text-steel text-xs">Added by {{ $link->adder->name }}</div>
            </div>
        @empty
            <p class="text-sm text-steel">No reference links added yet.</p>
        @endforelse
    </div>

    {{-- Status History --}}
    <div class="mb-6 bg-white border border-gray-200 rounded-lg p-4 print:border-none print:p-0">
        <flux:heading size="md" class="mb-3 font-serif text-ink">Status History</flux:heading>
        @forelse ($document->statusHistory as $entry)
            <div class="text-sm py-2 border-b border-gray-100 last:border-0">
                <span class="text-steel font-mono">{{ $entry->changed_at->format('M d, Y g:i A') }}</span>
                — {{ $entry->old_status ?? 'None' }} → <strong class="text-ledger-green">{{ $entry->new_status }}</strong>
                by {{ $entry->changer->name }}
                @if ($entry->remarks)
                    <div class="text-steel italic">{{ $entry->remarks }}</div>
                @endif
            </div>
        @empty
            <p class="text-sm text-steel">No status changes recorded yet.</p>
        @endforelse
    </div>

    {{-- Official Legislative Signatures (visible only on print) --}}
    <div class="hidden print:grid grid-cols-2 gap-12 mt-12 pt-8 text-center text-xs">
        <div>
            <p class="mb-14">ATTESTED BY:</p>
            <p class="font-bold uppercase tracking-wider border-t border-black pt-1 inline-block min-w-56">Secretary to the Sanggunian</p>
        </div>
        <div>
            <p class="mb-14">APPROVED &amp; CERTIFIED CORRECT:</p>
            <p class="font-bold uppercase tracking-wider border-t border-black pt-1 inline-block min-w-56">Municipal Vice Mayor / Presiding Officer</p>
        </div>
        @if ($document->document_type === 'Ordinance')
            <div class="col-span-2 mt-8 text-center">
                <p class="mb-14">APPROVED BY:</p>
                <p class="font-bold uppercase tracking-wider border-t border-black pt-1 inline-block min-w-56">Municipal Mayor</p>
                @if ($document->date_approved_mayor)
                    <p class="text-[10px] text-steel mt-1">Date: {{ $document->date_approved_mayor->format('F j, Y') }}</p>
                @endif
            </div>
        @endif
    </div>

    <div class="print:hidden">
        <flux:button href="{{ route('documents.index') }}" variant="ghost" class="!text-ink">← Back to Documents</flux:button>
    </div>
</div>