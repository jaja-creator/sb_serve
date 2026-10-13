<?php

namespace App\Livewire\Documents;

use App\Models\Document;
use App\Models\StatusHistory;
use App\Support\AuditLogger;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class Form extends Component
{
    use WithFileUploads;

    public ?Document $document = null;

    public string $series_number = '';
    public string $title = '';
    public string $document_type = 'Ordinance';
    public string $subject_matter = '';
    public string $sponsor = '';
    public string $status = 'Filed';
    public string $date_filed = '';
    public ?string $date_approved_mayor = null;
    public $scannedFile = null;
    public ?string $existingFilePath = null;

    public function mount(?Document $document = null): void
    {
        abort_unless(Auth::user()->isAdmin() || Auth::user()->isEncoder(), 403);

        if ($document?->exists) {
            if ($document->isLocked() && ! Auth::user()->isAdmin()) {
                abort(403, 'This measure is finalized and can only be edited by an administrator.');
            }

            $this->document = $document;
            $this->series_number = $document->series_number;
            $this->title = $document->title;
            $this->document_type = $document->document_type;
            $this->subject_matter = $document->subject_matter ?? '';
            $this->sponsor = $document->sponsor;
            $this->status = $document->status;
            $this->date_filed = $document->date_filed->format('Y-m-d');
            $this->date_approved_mayor = $document->date_approved_mayor?->format('Y-m-d');
            $this->existingFilePath = $document->file_path;
        }
    }

    public function removeExistingFile(): void
    {
        $this->existingFilePath = null;
    }

    public function getPhotoPreviewUrlProperty(): ?string
    {
        return $this->scannedFile?->temporaryUrl();
    }

    public function save(): void
    {
        $this->persist(asDraft: false);
    }

    public function saveDraft(): void
    {
        $this->status = 'Draft';
        $this->persist(asDraft: true);
    }

    protected function persist(bool $asDraft): void
    {
        if ($this->document && $this->document->isLocked() && ! Auth::user()->isAdmin()) {
            abort(403, 'This measure is finalized and can only be edited by an administrator.');
        }

        $validated = $this->validate([
            'series_number' => $asDraft ? 'nullable|string|max:50' : 'required|string|max:50',
            'title' => 'required|string|max:255',
            'document_type' => 'required|in:Ordinance,Resolution',
            'subject_matter' => 'nullable|string|max:255',
            'sponsor' => $asDraft ? 'nullable|string|max:150' : 'required|string|max:150',
            'date_filed' => $asDraft ? 'nullable|date' : 'required|date',
            'date_approved_mayor' => 'nullable|date',
            'scannedFile' => 'nullable|file|mimes:pdf|max:15360',
        ]);

        unset($validated['scannedFile']);

        if (! $this->document) {
            $validated['status'] = 'Filed';
        }

        if ($asDraft) {
            $validated['status'] = 'Draft';
            $validated['series_number'] = $validated['series_number'] ?: 'DRAFT-' . now()->format('YmdHis');
            $validated['sponsor'] = $validated['sponsor'] ?: 'To be determined';
            $validated['date_filed'] = $validated['date_filed'] ?: now()->toDateString();
        }

        if ($this->scannedFile) {
            $validated['file_path'] = $this->scannedFile->store('documents', 'public');
            $validated['file_hash'] = hash_file('sha256', $this->scannedFile->getRealPath());
        } elseif ($this->document && $this->existingFilePath === null) {
            $validated['file_path'] = null;
            $validated['file_hash'] = null;
        }

        if ($this->document) {
            $this->document->update($validated);
            AuditLogger::log(($asDraft ? 'Saved draft for document ' : 'Updated document ') . $this->document->series_number, $this->document->document_id);
        } else {
            $validated['encoded_by'] = Auth::id();
            $document = Document::create($validated);

            StatusHistory::create([
                'document_id' => $document->document_id,
                'old_status' => null,
                'new_status' => $validated['status'],
                'remarks' => 'Initial filing',
                'changed_by' => Auth::id(),
                'changed_at' => now(),
            ]);

            AuditLogger::log(($asDraft ? 'Drafted document ' : 'Encoded document ') . $document->series_number, $document->document_id);
        }

        session()->flash('success', $asDraft ? 'Draft saved. You can finish it anytime from the registry.' : 'Document saved successfully.');
        $this->redirect(route('documents.index'), navigate: true);
    }
}