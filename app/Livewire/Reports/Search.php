<?php

namespace App\Livewire\Reports;

use App\Models\Document;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Search extends Component
{
    use WithPagination;

    public string $search = '';

    public string $typeFilter = '';

    public string $statusFilter = '';

    public string $dateFrom = '';

    public string $dateTo = '';

    public string $sponsorFilter = '';

    public function mount(): void
    {
        abort_unless(Auth::check(), 403);
    }

    public function updating($property): void
    {
        if (in_array($property, ['search', 'typeFilter', 'statusFilter', 'dateFrom', 'dateTo', 'sponsorFilter'])) {
            $this->resetPage();
        }
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'typeFilter', 'statusFilter', 'dateFrom', 'dateTo', 'sponsorFilter']);
    }

    protected function filteredQuery()
    {
        return Document::query()
            ->with(['encoder', 'ordinance', 'resolution'])
            ->when($this->search, fn ($query) => $query->where(
                fn ($q) => $q->where('title', 'like', "%{$this->search}%")
                    ->orWhere('series_number', 'like', "%{$this->search}%")
                    ->orWhere('subject_matter', 'like', "%{$this->search}%")
            ))
            ->when($this->typeFilter, fn ($q) => $q->where('document_type', $this->typeFilter))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->sponsorFilter, fn ($q) => $q->where('sponsor', 'like', "%{$this->sponsorFilter}%"))
            ->when($this->dateFrom, fn ($q) => $q->whereDate('date_filed', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->whereDate('date_filed', '<=', $this->dateTo))
            ->orderBy('series_number');
    }

    public function exportCsv()
    {
        abort_unless(Auth::user()->isAdmin(), 403);

        $documents = $this->filteredQuery()->get();

        $filename = 'sb-serve-report-'.now()->format('Y-m-d-His').'.csv';

        return response()->streamDownload(function () use ($documents) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Series No.', 'Title', 'Type', 'Sponsor', 'Status', 'Date Filed', 'Encoded By']);

            foreach ($documents as $doc) {
                fputcsv($handle, [
                    $doc->series_number,
                    $doc->title,
                    $doc->document_type,
                    $doc->sponsor,
                    $doc->status,
                    $doc->date_filed->format('Y-m-d'),
                    $doc->encoder?->name ?? '—',
                ]);
            }

            fclose($handle);
        }, $filename);
    }

    public function render()
    {
        return view('livewire.reports.search', [
            'documents' => $this->filteredQuery()->paginate(15),
        ]);
    }
}
