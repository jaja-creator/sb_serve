<?php

namespace App\Livewire\Public;

use App\Mail\CitizenProposalSubmitted;
use App\Models\CitizenProposal;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class Propose extends Component
{
    public string $activeTab = 'propose'; // 'propose' or 'track'

    public string $proposal_type = 'Ordinance';

    public string $title = '';

    public string $description = '';

    public string $proposer_name = '';

    public string $proposer_email = '';

    public string $proposer_phone = '';

    public string $proposer_barangay = '';

    // Honeypot — real users never see or fill this; bots often do
    public string $website = '';

    public bool $submitted = false;

    public ?int $referenceId = null;

    // Tracking properties
    public string $trackReference = '';

    public string $trackEmail = '';

    public ?CitizenProposal $trackedProposal = null;

    public ?string $trackError = null;

    public function submit(): void
    {
        if ($this->website !== '') {
            // Silently drop likely spam without alerting the bot
            $this->submitted = true;

            return;
        }

        $this->validate([
            'proposal_type' => 'required|in:Ordinance,Resolution',
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:20',
            'proposer_name' => 'required|string|max:150',
            'proposer_email' => 'required|email',
            'proposer_phone' => 'nullable|string|max:30',
            'proposer_barangay' => 'nullable|string|max:100',
        ]);

        $proposal = CitizenProposal::create([
            'proposal_type' => $this->proposal_type,
            'title' => $this->title,
            'description' => $this->description,
            'proposer_name' => $this->proposer_name,
            'proposer_email' => $this->proposer_email,
            'proposer_phone' => $this->proposer_phone ?: null,
            'proposer_barangay' => $this->proposer_barangay ?: null,
            'status' => 'Pending',
        ]);

        try {
            $recipient = SystemSetting::get('contact_email') ?: config('mail.from.address');
            if ($recipient) {
                Mail::to($recipient)->send(new CitizenProposalSubmitted($proposal));
            }
        } catch (\Throwable $e) {
            // Never let a mail-server hiccup block a citizen's submission —
            // the proposal is already saved; just log the failure.
            report($e);
        }

        $this->referenceId = $proposal->proposal_id;
        $this->submitted = true;
        $this->reset(['title', 'description', 'proposer_name', 'proposer_email', 'proposer_phone', 'proposer_barangay']);
    }

    public function resetForm(): void
    {
        $this->submitted = false;
        $this->referenceId = null;
    }

    public function track(): void
    {
        $this->validate([
            'trackReference' => 'required|string',
            'trackEmail' => 'required|email',
        ]);

        $this->trackedProposal = null;
        $this->trackError = null;

        $cleanedId = preg_replace('/[^0-9]/', '', $this->trackReference);

        if (! $cleanedId) {
            $this->trackError = 'Please enter a valid Reference Number (e.g., SB-P-10 or 10).';

            return;
        }

        $proposal = CitizenProposal::where('proposal_id', (int) $cleanedId)
            ->where('proposer_email', trim($this->trackEmail))
            ->first();

        if (! $proposal) {
            $this->trackError = 'No proposal was found matching Reference No. '.$this->trackReference.' and email '.$this->trackEmail.'.';

            return;
        }

        $this->trackedProposal = $proposal;
    }

    public function clearTrack(): void
    {
        $this->trackReference = '';
        $this->trackEmail = '';
        $this->trackedProposal = null;
        $this->trackError = null;
    }

    public function render()
    {
        return view('livewire.public.propose')
            ->layout('layouts.public');
    }
}
