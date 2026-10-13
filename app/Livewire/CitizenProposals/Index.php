<?php

namespace App\Livewire\CitizenProposals;

use App\Mail\CitizenProposalForwarded;
use App\Mail\CitizenProposalStatusUpdated;
use App\Models\CitizenProposal;
use App\Models\SystemSetting;
use App\Support\AuditLogger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url]
    public string $statusFilter = '';

    public ?int $editingProposalId = null;

    public string $editingStatus = '';

    public string $adminRemarks = '';

    public function mount(): void
    {
        abort_unless(Auth::user()->isAdmin() || Auth::user()->isEncoder(), 403);
    }

    public function startReview(int $proposalId, string $status): void
    {
        $this->editingProposalId = $proposalId;
        $this->editingStatus = $status;
        $proposal = CitizenProposal::findOrFail($proposalId);
        $this->adminRemarks = $proposal->admin_remarks ?? '';
    }

    public function cancelReview(): void
    {
        $this->editingProposalId = null;
        $this->editingStatus = '';
        $this->adminRemarks = '';
    }

    public function confirmReview(): void
    {
        if (! $this->editingProposalId || ! $this->editingStatus) {
            return;
        }

        $this->updateStatus($this->editingProposalId, $this->editingStatus, $this->adminRemarks);
        $this->cancelReview();
    }

    public function updateStatus(int $proposalId, string $status, ?string $remarks = null): void
    {
        abort_unless(Auth::user()->isAdmin() || Auth::user()->isEncoder(), 403);
        abort_unless(in_array($status, CitizenProposal::STATUSES), 400);

        $proposal = CitizenProposal::findOrFail($proposalId);
        $data = [
            'status' => $status,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ];

        if ($remarks !== null) {
            $data['admin_remarks'] = $remarks;
        }

        $proposal->update($data);

        AuditLogger::log("Marked citizen proposal '{$proposal->title}' as {$status}");

        // Notify citizen of the status update
        if ($proposal->proposer_email) {
            try {
                Mail::to($proposal->proposer_email)->send(new CitizenProposalStatusUpdated($proposal));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        // Forward notice to SB office if applicable
        if ($status === 'Forwarded to SB') {
            $recipient = SystemSetting::get('contact_email') ?: config('mail.from.address');
            if ($recipient) {
                try {
                    Mail::to($recipient)->send(new CitizenProposalForwarded($proposal));
                } catch (\Throwable $e) {
                    report($e);
                }
            }
        }

        session()->flash('success', 'Proposal updated and notification sent.');
    }

    public function render()
    {
        $proposals = CitizenProposal::query()
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->latest()
            ->paginate(10);

        return view('livewire.citizen-proposals.index', ['proposals' => $proposals]);
    }
}
