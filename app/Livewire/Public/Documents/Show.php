<?php

namespace App\Livewire\Public\Documents;

use App\Models\Document;
use Livewire\Component;

class Show extends Component
{
    public Document $document;

    public function mount(Document $document): void
    {
        abort_if($document->status === 'Draft', 404);

        $this->document = $document->load([
            'ordinance', 'resolution',
            'committeeReports.preparer',
            'amendments' => fn ($q) => $q->where('amendment_status', 'Approved'),
            'amendments.submitter',
            'statusHistory.changer',
            'links.adder',
        ]);
    }

    public function render()
    {
        return view('livewire.public.documents.show')
            ->layout('layouts.public');
    }
}