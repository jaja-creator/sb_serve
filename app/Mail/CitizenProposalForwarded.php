<?php

namespace App\Mail;

use App\Models\CitizenProposal;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CitizenProposalForwarded extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public CitizenProposal $proposal)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Citizen Proposal Forwarded: ' . $this->proposal->title,
        );
    }

public function content(): Content
{
    return new Content(
        markdown: 'emails.citizen-proposal-forwarded',
    );
}
}