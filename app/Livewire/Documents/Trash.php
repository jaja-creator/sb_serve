<?php

namespace App\Livewire\Documents;

use App\Models\Document;
use App\Support\AuditLogger;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Trash extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    public function mount(): void
    {
        abort_unless(Auth::user()->isAdmin(), 403);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function restore(int $documentId): void
    {
        abort_unless(Auth::user()->isAdmin(), 403);

        $document = Document::onlyTrashed()->findOrFail($documentId);
        $document->restore();

        AuditLogger::log('Restored document ' . $document->series_number, $document->document_id);

        session()->flash('success', 'Document restored successfully.');
    }

    public function render()
    {
        $documents = Document::onlyTrashed()
            ->when($this->search, fn ($query) => $query->where(
                fn ($q) => $q->where('title', 'like', "%{$this->search}%")
                    ->orWhere('series_number', 'like', "%{$this->search}%")
            ))
            ->latest('deleted_at')
            ->paginate(10);

        return view('livewire.documents.trash', [
            'documents' => $documents,
        ]);
    }
}