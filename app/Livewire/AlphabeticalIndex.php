<?php

namespace App\Livewire;

use App\Models\Document;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AlphabeticalIndex extends Component
{
    public function mount(): void
    {
        abort_unless(Auth::check(), 403);
    }

    public function render()
    {
        $documents = Document::query()
            ->orderByRaw('COALESCE(NULLIF(subject_matter, ""), title) asc')
            ->get()
            ->groupBy(function ($doc) {
                $topic = $doc->subject_matter ?: $doc->title;
                return strtoupper(substr($topic, 0, 1));
            });

        $availableLetters = $documents->keys()->sort()->values();

        return view('livewire.alphabetical-index', [
            'groupedDocuments' => $documents,
            'availableLetters' => $availableLetters,
        ]);
    }
}
