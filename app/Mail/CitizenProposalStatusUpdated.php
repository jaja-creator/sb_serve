<?php

namespace App\Mail;

use App\Models\CitizenProposal;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CitizenProposalStatusUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public CitizenProposal $proposal) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Update on Your Proposal: SB-P-{$this->proposal->proposal_id} ({$this->proposal->status})",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.citizen-proposal-status-updated',
        );
    }
}
