<?php

namespace App\Livewire\Public;

use App\Models\Document;
use Livewire\Component;

class Home extends Component
{
    public string $search = '';

    public function goToSearch(): void
    {
        $this->redirect(route('public.documents.index', ['search' => $this->search]));
    }

public function render()
{
    $statusMeanings = [
        'Filed' => 'submitted and awaiting first reading',
        'First Reading' => 'formally introduced to the council',
        'For Committee Reporting' => 'being studied by a committee',
        'Second Reading' => 'reviewed and debated by the full council',
        'Third Reading' => 'up for final council vote',
        'Approved' => 'passed by the Sangguniang Bayan',
        'Enacted' => 'signed into law by the Mayor',
        'Vetoed' => 'rejected by the Mayor',
        'Returned' => 'sent back by the Mayor for revision',
        'Archived' => 'no longer active',
    ];

    $recent = Document::query()
        ->where('status', '!=', 'Draft')
        ->when($this->search !== '', function ($query) {
            $query->where(function ($q) {
                $q->where('title', 'like', "%{$this->search}%")
                    ->orWhere('series_number', 'like', "%{$this->search}%")
                    ->orWhere('sponsor', 'like', "%{$this->search}%");
            });
        })
        ->latest('date_filed')
        ->take($this->search !== '' ? 10 : 5)
        ->get();

    $legend = $recent->pluck('status')->unique()->mapWithKeys(fn ($status) => [
        $status => $statusMeanings[$status] ?? null,
    ])->filter();

    $totalOrdinances = Document::where('status', '!=', 'Draft')->where('document_type', 'Ordinance')->count();
    $totalResolutions = Document::where('status', '!=', 'Draft')->where('document_type', 'Resolution')->count();
    $totalEnacted = Document::where('status', 'Enacted')->count();

    return view('livewire.public.home', [
        'recent' => $recent,
        'legend' => $legend,
        'totalOrdinances' => $totalOrdinances,
        'totalResolutions' => $totalResolutions,
        'totalEnacted' => $totalEnacted,
    ])
        ->layout('layouts.public');
}
}