<?php

namespace App\Mail;

use App\Models\CitizenProposal;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CitizenProposalSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public CitizenProposal $proposal) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "New Citizen Proposal: SB-P-{$this->proposal->proposal_id} — {$this->proposal->title}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.citizen-proposal-submitted',
        );
    }
}
