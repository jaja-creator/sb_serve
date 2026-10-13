<?php

namespace App\Livewire\Documents;

use App\Models\Document;
use App\Support\AuditLogger;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $typeFilter = '';

    #[Url]
    public string $statusFilter = '';

    #[Url]
    public string $sortField = 'date_filed';

    #[Url]
    public string $sortDirection = 'desc';

    public const SORTABLE_FIELDS = [
        'date_filed' => 'Date Filed',
        'title' => 'Title',
        'series_number' => 'Series Number',
        'sponsor' => 'Sponsor',
        'status' => 'Status',
    ];

    public function mount(): void
    {
        abort_unless(Auth::check(), 403);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingTypeFilter(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    public function deleteDocument(int $documentId): void
    {
        abort_unless(Auth::user()->canDelete(), 403);

        $document = Document::findOrFail($documentId);
        AuditLogger::log('Deleted document ' . $document->series_number, $documentId);
        $document->delete();

        session()->flash('success', 'Document deleted successfully.');
    }

    public function render()
    {
        $documents = Document::query()
            ->when($this->search, fn ($query) => $query->where(
                fn ($q) => $q->where('title', 'like', "%{$this->search}%")
                    ->orWhere('series_number', 'like', "%{$this->search}%")
                    ->orWhere('sponsor', 'like', "%{$this->search}%")
            ))
            ->when($this->typeFilter, fn ($query) => $query
                ->where('document_type', $this->typeFilter))
            ->when($this->statusFilter, fn ($query) => $query
                ->where('status', $this->statusFilter))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.documents.index', [
            'documents' => $documents,
        ]);
    }
}