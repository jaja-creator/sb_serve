<?php

namespace App\Livewire\Public\Documents;

use App\Models\Document;
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

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingTypeFilter(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $documents = Document::query()
            ->where('status', '!=', 'Draft')
            ->when($this->search, fn ($q) => $q->where(
                fn ($qq) => $qq->where('title', 'like', "%{$this->search}%")
                    ->orWhere('series_number', 'like', "%{$this->search}%")
                    ->orWhere('subject_matter', 'like', "%{$this->search}%")
            ))
            ->when($this->typeFilter, fn ($q) => $q->where('document_type', $this->typeFilter))
            ->latest('date_filed')
            ->paginate(10);

        return view('livewire.public.documents.index', ['documents' => $documents])
            ->layout('layouts.public');
    }
}