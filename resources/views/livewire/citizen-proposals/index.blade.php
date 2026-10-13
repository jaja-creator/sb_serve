<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <flux:heading size="lg" class="font-serif text-ink">Citizen Proposals</flux:heading>
            <p class="text-sm text-steel mt-1">Ordinance/resolution proposals submitted by residents</p>
        </div>
        <flux:select wire:model.live="statusFilter" placeholder="All Statuses" class="w-48 !text-ink [&_button]:border [&_button]:border-gray-300 [&_button]:px-3 [&_button]:py-2">
            <flux:select.option value="">All Statuses</flux:select.option>
            @foreach (\App\Models\CitizenProposal::STATUSES as $status)
                <flux:select.option value="{{ $status }}">{{ $status }}</flux:select.option>
            @endforeach
        </flux:select>
    </div>

    @if (session('success'))
        <flux:callout variant="success" class="mb-4">{{ session('success') }}</flux:callout>
    @endif

    <div class="border border-gray-300 rounded-lg divide-y divide-gray-100">
        @forelse ($proposals as $proposal)
            <div class="p-4">
                <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <flux:badge>{{ $proposal->proposal_type }}</flux:badge>
                            <span class="text-xs font-mono text-steel bg-gray-100 px-2 py-0.5 rounded">Ref: SB-P-{{ $proposal->proposal_id }}</span>
                            <span class="text-xs font-semibold uppercase px-2 py-0.5 rounded-full
                                {{ match($proposal->status) {
                                    'Pending' => 'bg-gray-100 text-gray-700',
                                    'Under Review' => 'bg-amber-50 text-amber-700',
                                    'Forwarded to SB' => 'bg-green-50 text-green-700',
                                    'Declined' => 'bg-red-50 text-red-700',
                                    default => 'bg-gray-100 text-gray-700',
                                } }}">
                                {{ $proposal->status }}
                            </span>
                        </div>
                        <p class="font-serif font-semibold text-ink">{{ $proposal->title }}</p>
                        <p class="text-sm text-steel mt-1">{{ Str::limit($proposal->description, 200) }}</p>

                        @if ($proposal->admin_remarks)
                            <div class="mt-2 text-xs bg-amber-50 border border-amber-200 text-amber-900 rounded p-2">
                                <strong>Sanggunian Remarks:</strong> {{ $proposal->admin_remarks }}
                            </div>
                        @endif

                        <p class="text-xs text-steel font-mono mt-2">
                            {{ $proposal->proposer_name }} &middot; {{ $proposal->proposer_email }}
                            @if ($proposal->proposer_barangay) &middot; Brgy. {{ $proposal->proposer_barangay }} @endif
                            &middot; {{ $proposal->created_at->format('M d, Y') }}
                            @if ($proposal->reviewed_at)
                                &middot; Reviewed: {{ $proposal->reviewed_at->format('M d, Y') }}
                            @endif
                        </p>
                    </div>
                </div>

                @if ($editingProposalId === $proposal->proposal_id)
                    <div class="mt-3 p-3 bg-gray-50 border border-gray-200 rounded-lg space-y-3">
                        <p class="text-xs font-semibold uppercase text-ink tracking-wider">
                            Update Status to: <span class="text-seal-gold font-bold">{{ $editingStatus }}</span>
                        </p>
                        <flux:textarea
                            wire:model="adminRemarks"
                            placeholder="Optional remarks/notes for the constituent..."
                            rows="2"
                            class="[&_textarea]:!text-ink [&_textarea]:!border [&_textarea]:!border-gray-300"
                        />
                        <div class="flex gap-2">
                            <flux:button wire:click="confirmReview" variant="primary" size="sm">Save & Notify Citizen</flux:button>
                            <flux:button wire:click="cancelReview" variant="ghost" size="sm" class="!text-ink">Cancel</flux:button>
                        </div>
                    </div>
                @else
                    <div class="flex gap-3 mt-3 pt-3 border-t border-gray-100">
                        <button wire:click="startReview({{ $proposal->proposal_id }}, 'Under Review')" class="text-sm font-medium text-amber-700 hover:text-amber-800">Mark Under Review</button>
                        <span class="text-gray-300">&middot;</span>
                        <button wire:click="startReview({{ $proposal->proposal_id }}, 'Forwarded to SB')" class="text-sm font-medium text-ledger-green hover:text-ledger-green-dark">Forward to SB</button>
                        <span class="text-gray-300">&middot;</span>
                        <button wire:click="startReview({{ $proposal->proposal_id }}, 'Declined')" class="text-sm font-medium text-red-600 hover:text-red-700">Decline</button>
                    </div>
                @endif
            </div>
        @empty
            <p class="p-10 text-center text-steel">No proposals submitted yet.</p>
        @endforelse
    </div>

    <div class="mt-4">{{ $proposals->links() }}</div>
</div>
