<div class="p-6 max-w-2xl mx-auto">
    {{-- Header --}}
    <div class="mb-6 text-center">
        <flux:heading size="xl" class="font-serif text-ink">Citizen Participation</flux:heading>
        <p class="text-sm text-steel mt-1">Submit a proposed ordinance or resolution, or track the progress of your submission.</p>

        {{-- Mode Switcher Tabs --}}
        <div class="inline-flex rounded-lg border border-gray-300 p-1 bg-gray-50 mt-4">
            <button
                type="button"
                wire:click="$set('activeTab', 'propose')"
                class="px-4 py-2 text-sm font-medium rounded-md transition-colors {{ $activeTab === 'propose' ? 'bg-white text-ink shadow-sm font-semibold' : 'text-steel hover:text-ink' }}"
            >
                Propose a Measure
            </button>
            <button
                type="button"
                wire:click="$set('activeTab', 'track')"
                class="px-4 py-2 text-sm font-medium rounded-md transition-colors {{ $activeTab === 'track' ? 'bg-white text-ink shadow-sm font-semibold' : 'text-steel hover:text-ink' }}"
            >
                Track a Proposal
            </button>
        </div>
    </div>

    @if ($activeTab === 'propose')
        @if ($submitted)
            <div class="border border-green-300 bg-green-50 rounded-lg p-6 text-center">
                <flux:icon name="check-circle" class="size-8 text-green-600 mx-auto mb-2" />
                <p class="font-semibold text-ink mb-1">Thank you — your proposal has been submitted.</p>
                <p class="text-sm text-steel mb-3">SB staff will review it and you will receive email updates as it progresses.</p>
                @if ($referenceId)
                    <div class="my-3">
                        <p class="text-xs text-steel uppercase tracking-widest font-mono">Your Tracking Reference</p>
                        <p class="text-base font-bold font-mono text-ledger-green bg-white border border-gray-200 rounded px-4 py-2 inline-block mt-1">
                            SB-P-{{ $referenceId }}
                        </p>
                    </div>
                    <p class="text-xs text-steel mt-2">
                        You can use this reference number along with your email to track its status anytime on this page.
                    </p>
                @endif
                <div class="flex justify-center gap-3 mt-4">
                    <flux:button wire:click="resetForm" variant="primary">Submit Another</flux:button>
                    <flux:button wire:click="$set('activeTab', 'track')" variant="ghost" class="!text-ink">Track a Proposal</flux:button>
                </div>
            </div>
        @else
            <form wire:submit="submit" class="space-y-6">
                <input type="text" wire:model="website" class="hidden" tabindex="-1" autocomplete="off" />

                <div class="border border-gray-300 rounded-lg p-6 space-y-4 bg-white shadow-sm">
                    <flux:select wire:model="proposal_type" label="Type of Measure" class="!text-ink [&_button]:border [&_button]:border-gray-300 [&_button]:px-3 [&_button]:py-2">
                        <flux:select.option value="Ordinance">Ordinance</flux:select.option>
                        <flux:select.option value="Resolution">Resolution</flux:select.option>
                    </flux:select>

                    <flux:input wire:model="title" label="Proposed Title" placeholder="e.g. An Ordinance Establishing a Barangay Organic Waste Facility" class="!text-ink [&_input]:border [&_input]:border-gray-300 [&_input]:px-3 [&_input]:py-2" />

                    <flux:textarea wire:model="description" label="Description / Rationale" rows="6" placeholder="Describe what you are proposing, the problem it addresses, and why the Sanggunian should consider it." class="!text-ink [&_textarea]:border [&_textarea]:border-gray-300" />
                </div>

                <div class="border border-gray-300 rounded-lg p-6 space-y-4 bg-white shadow-sm">
                    <p class="text-xs uppercase tracking-widest text-steel font-mono font-semibold">Your Contact Details</p>
                    <flux:input wire:model="proposer_name" label="Full Name" placeholder="Juan Dela Cruz" class="!text-ink [&_input]:border [&_input]:border-gray-300 [&_input]:px-3 [&_input]:py-2" />
                    <flux:input wire:model="proposer_email" label="Email Address" type="email" placeholder="juan@example.com" class="!text-ink [&_input]:border [&_input]:border-gray-300 [&_input]:px-3 [&_input]:py-2" />
                    <div class="grid grid-cols-2 gap-4">
                        <flux:input wire:model="proposer_phone" label="Phone (optional)" placeholder="0917-000-0000" class="!text-ink [&_input]:border [&_input]:border-gray-300 [&_input]:px-3 [&_input]:py-2" />
                        <flux:input wire:model="proposer_barangay" label="Barangay (optional)" placeholder="Centro 01" class="!text-ink [&_input]:border [&_input]:border-gray-300 [&_input]:px-3 [&_input]:py-2" />
                    </div>
                </div>

                <flux:button type="submit" variant="primary" class="w-full" wire:loading.attr="disabled" wire:target="submit">
                    <span wire:loading.remove wire:target="submit">Submit Proposal</span>
                    <span wire:loading wire:target="submit">Submitting...</span>
                </flux:button>
            </form>
        @endif
    @else
        {{-- Track a Proposal Tab --}}
        <div class="space-y-6">
            <div class="border border-gray-300 rounded-lg p-6 bg-white shadow-sm">
                <form wire:submit="track" class="space-y-4">
                    <div>
                        <flux:input
                            wire:model="trackReference"
                            label="Proposal Reference Number"
                            placeholder="e.g. SB-P-10 or 10"
                            class="!text-ink [&_input]:border [&_input]:border-gray-300 [&_input]:px-3 [&_input]:py-2"
                        />
                    </div>
                    <div>
                        <flux:input
                            wire:model="trackEmail"
                            label="Email Address (used during submission)"
                            type="email"
                            placeholder="your-email@example.com"
                            class="!text-ink [&_input]:border [&_input]:border-gray-300 [&_input]:px-3 [&_input]:py-2"
                        />
                    </div>
                    <div class="flex gap-2 pt-2">
                        <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="track">
                            <span wire:loading.remove wire:target="track">Track Status</span>
                            <span wire:loading wire:target="track">Looking up...</span>
                        </flux:button>
                        @if ($trackedProposal || $trackError)
                            <flux:button type="button" wire:click="clearTrack" variant="ghost" class="!text-ink">Clear</flux:button>
                        @endif
                    </div>
                </form>
            </div>

            @if ($trackError)
                <flux:callout variant="danger">
                    {{ $trackError }}
                </flux:callout>
            @endif

            @if ($trackedProposal)
                <div class="border border-gray-300 rounded-lg p-6 bg-white shadow-sm space-y-4">
                    <div class="flex items-start justify-between gap-4 border-b border-gray-100 pb-4">
                        <div>
                            <span class="text-xs font-mono text-steel uppercase">Reference No. SB-P-{{ $trackedProposal->proposal_id }}</span>
                            <flux:heading size="lg" class="font-serif text-ink mt-1">{{ $trackedProposal->title }}</flux:heading>
                            <p class="text-xs text-steel mt-1">Submitted on {{ $trackedProposal->created_at->format('F j, Y g:i A') }}</p>
                        </div>
                        <span class="text-xs font-semibold uppercase px-3 py-1 rounded-full whitespace-nowrap
                            {{ match($trackedProposal->status) {
                                'Pending' => 'bg-gray-100 text-gray-700',
                                'Under Review' => 'bg-amber-100 text-amber-800',
                                'Forwarded to SB' => 'bg-green-100 text-green-800',
                                'Declined' => 'bg-red-100 text-red-800',
                                default => 'bg-gray-100 text-gray-700',
                            } }}">
                            {{ $trackedProposal->status }}
                        </span>
                    </div>

                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-steel font-mono">Measure Type</p>
                        <p class="text-sm text-ink mt-0.5">{{ $trackedProposal->proposal_type }}</p>
                    </div>

                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-steel font-mono">Submitted Description</p>
                        <p class="text-sm text-steel mt-0.5 whitespace-pre-line">{{ $trackedProposal->description }}</p>
                    </div>

                    @if ($trackedProposal->admin_remarks)
                        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                            <p class="text-xs font-bold uppercase tracking-widest text-amber-900 font-mono">Office Notes &amp; Remarks</p>
                            <p class="text-sm text-amber-950 mt-1 italic">{{ $trackedProposal->admin_remarks }}</p>
                            @if ($trackedProposal->reviewed_at)
                                <p class="text-[11px] text-amber-800 mt-2">Reviewed on {{ $trackedProposal->reviewed_at->format('F j, Y') }}</p>
                            @endif
                        </div>
                    @endif
                </div>
            @endif
        </div>
    @endif
</div>