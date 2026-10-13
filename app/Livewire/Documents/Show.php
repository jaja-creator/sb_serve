<?php

namespace App\Livewire\Documents;

use App\Models\Amendment;
use App\Models\CommitteeReport;
use App\Models\Document;
use App\Models\DocumentLink;
use App\Models\Ordinance;
use App\Models\Resolution;
use App\Models\StatusHistory;
use App\Support\AuditLogger;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Validation\Rule;

class Show extends Component
{
    use WithFileUploads;

    public Document $document;

    public bool $showStageForm = false;
    public string $stage = 'Draft';
    public string $version_number = '1.0';
    public string $author = '';
    public string $committee_referred = '';
    public string $purpose = '';
    public string $date_prepared = '';
    public string $implementing_agency = '';
    public string $target_beneficiaries = '';
    public string $remarks = '';

    public bool $showStatusForm = false;
    public string $newStatus = '';
    public string $passage_mode = 'regular';
    public ?string $date_enacted = null;
    public ?string $date_calendared = null;
    public ?string $date_referred_committee = null;
    public ?string $date_second_reading = null;
    public ?string $date_third_reading = null;
    public ?string $date_approved = null;
    public ?string $date_vetoed = null;
    public ?string $date_returned = null;
    public string $statusRemarks = '';

    public bool $showReportForm = false;
    public string $committee_name = '';
    public string $report_title = '';
    public string $report_date = '';
    public string $recommendation = 'For Approval';
    public string $findings = '';

    public const RECOMMENDATIONS = ['For Approval', 'For Amendment', 'For Rejection', 'For Further Study'];

    public bool $showAmendmentForm = false;
    public string $section_amended = '';
    public string $amendment_description = '';
    public string $amendment_date = '';

    public const AMENDMENT_STATUSES = ['Pending', 'Approved', 'Rejected'];

    public bool $showLinkForm = false;
    public string $link_type = 'Related Ordinance';
    public string $reference_title = '';
    public string $reference_number = '';
    public string $source_url = '';
    public $referenceFile = null;

    public const LINK_TYPES = ['Related Ordinance', 'Related Resolution', 'Legal Basis', 'Supporting Document', 'Other'];

    public function mount(Document $document): void
    {
        $this->document = $document->load([
            'encoder', 'ordinance', 'resolution',
            'committeeReports.preparer', 'amendments.submitter', 'amendments.reviewer',
            'statusHistory.changer', 'links.adder',
        ]);

        $this->newStatus = $document->status;
        $this->passage_mode = $document->passage_mode ?? 'regular';
        $this->date_enacted = $document->date_enacted?->format('Y-m-d');
        $this->date_calendared = $document->date_calendared?->format('Y-m-d');
        $this->date_referred_committee = $document->date_referred_committee?->format('Y-m-d');
        $this->date_second_reading = $document->date_second_reading?->format('Y-m-d');
        $this->date_third_reading = $document->date_third_reading?->format('Y-m-d');
        $this->date_approved = $document->date_approved?->format('Y-m-d');
        $this->date_vetoed = $document->date_vetoed?->format('Y-m-d');
        $this->date_returned = $document->date_returned?->format('Y-m-d');
        $this->author = Auth::user()->name;
        $this->date_prepared = now()->format('Y-m-d');
        $this->report_date = now()->format('Y-m-d');
        $this->amendment_date = now()->format('Y-m-d');
    }

    public const STANDING_COMMITTEES = [
        'Committee on Amendments, Laws and Legal Matters',
        'Committee on Peace and Order, Fire and Public Safety, Justice, Human Rights, Accountability of Public Officers and Investigation',
        'Committee on Food, Agriculture, Fisheries, Livestock and Agrarian Reform',
        'Committee on Labor, Employment, Civil Service and Human Resource Development',
        'Committee on Social Services, Senior Citizens, PWD Affairs and Minority/Indigenous People',
        'Committee on Women, Children, Family Relation and Gender Equality',
        'Committee on Youth and Sports Development',
        'Committee on Information, Science and Communication Technology',
        'Committee on Infrastructure and Public Works',
        'Committee on Environment, Ecology, Energy, Natural and Renewable Resources',
        'Committee on Climate Change Mitigation, Risk Reduction and Disaster Resilience',
        'Committee on Transportation and Public Utilities',
        'Committee on Barangay Affairs and Poverty Alleviation',
        'Committee on Zoning, Housing and Land Use, Rural and Urban Development, Squatters and Resettlement',
        'Committee on Tourism and Special Events',
        'Committee on Health and Sanitation',
        'Committee on Education, Culture and the Arts',
        'Committee on Budget, Appropriations, Taxation, Ways and Means and Financial Institutions',
        'Committee on Public Market, Private Market, Trade, Entrepreneurship and Abbattoirs',
        "Committee on People's Participation, Civil Society Organizations and Cooperative Development",
        'Legislative Oversight Committee',
    ];

    public function verifyFileIntegrity(): void
    {
        if (! $this->document->file_path || ! $this->document->file_hash) {
            session()->flash('error', 'No file or hash on record to verify.');
            return;
        }

        $fullPath = storage_path('app/public/' . $this->document->file_path);

        if (! file_exists($fullPath)) {
            session()->flash('error', 'File is missing from storage.');
            return;
        }

        $currentHash = hash_file('sha256', $fullPath);

        if ($currentHash === $this->document->file_hash) {
            session()->flash('success', 'File integrity verified — no changes detected since upload.');
        } else {
            session()->flash('error', 'WARNING: File hash mismatch. This file may have been altered since upload.');
        }
    }

    public function saveOrdinance(): void
    {
        abort_unless(Auth::user()->isAdmin() || Auth::user()->isEncoder(), 403);

        $validated = $this->validate([
            'stage' => 'required|string|max:20',
            'version_number' => 'required|string|max:20',
            'author' => 'required|string|max:150',
            'committee_referred' => 'nullable|string|max:150',
            'date_prepared' => 'required|date',
            'implementing_agency' => 'nullable|string|max:150',
            'target_beneficiaries' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        $validated['document_id'] = $this->document->document_id;
        $validated['encoded_by'] = Auth::id();

        Ordinance::create($validated);

        $this->document->refresh()->load('ordinance');
        $this->showStageForm = false;
        session()->flash('success', 'Ordinance details added.');
    }

    public function saveResolution(): void
    {
        abort_unless(Auth::user()->isAdmin() || Auth::user()->isEncoder(), 403);

        $validated = $this->validate([
            'stage' => 'required|string|max:20',
            'version_number' => 'required|string|max:20',
            'author' => 'required|string|max:150',
            'purpose' => 'nullable|string|max:255',
            'date_prepared' => 'required|date',
            'implementing_agency' => 'nullable|string|max:150',
            'target_beneficiaries' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        $validated['document_id'] = $this->document->document_id;
        $validated['encoded_by'] = Auth::id();

        Resolution::create($validated);

        $this->document->refresh()->load('resolution');
        $this->showStageForm = false;
        session()->flash('success', 'Resolution details added.');
    }

    public function changeStatus(): void
    {
        abort_unless(Auth::user()->isAdmin() || Auth::user()->isEncoder(), 403);

        if ($this->document->isLocked() && ! Auth::user()->isAdmin()) {
            abort(403, 'This measure is finalized and can only be reopened by an administrator.');
        }

        $rules = [
            'newStatus' => 'required|in:' . implode(',', $this->document->availableStatuses()),
            'statusRemarks' => 'nullable|string',
        ];

        switch ($this->newStatus) {
            case 'First Reading':
                $rules['date_calendared'] = 'required|date';
                break;
            case 'For Committee Reporting':
                $rules['date_referred_committee'] = 'required|date';
                break;
            case 'Second Reading':
                $rules['date_second_reading'] = 'required|date';
                break;
            case 'Third Reading':
                $rules['date_third_reading'] = 'required|date';
                break;
            case 'Approved':
                $rules['date_approved'] = 'required|date';
                break;
            case 'Enacted':
                $rules['date_enacted'] = 'required|date';
                $rules['passage_mode'] = 'required|in:' . implode(',', array_keys(Document::PASSAGE_MODES));
                break;
            case 'Vetoed':
                $rules['date_vetoed'] = 'required|date';
                break;
            case 'Returned':
                $rules['date_returned'] = 'required|date';
                break;
        }

        $this->validate($rules);

        $oldStatus = $this->document->status;

        $remarks = $this->statusRemarks;
        if ($this->newStatus === 'Enacted' && $this->passage_mode !== 'regular') {
            $remarks = trim(($remarks ? $remarks . ' — ' : '') . Document::PASSAGE_MODES[$this->passage_mode]);
        }

        StatusHistory::create([
            'document_id' => $this->document->document_id,
            'old_status' => $oldStatus,
            'new_status' => $this->newStatus,
            'remarks' => $remarks,
            'changed_by' => Auth::id(),
            'changed_at' => now(),
        ]);

        $updateData = ['status' => $this->newStatus];

        switch ($this->newStatus) {
            case 'First Reading':
                $updateData['date_calendared'] = $this->date_calendared;
                break;
            case 'For Committee Reporting':
                $updateData['date_referred_committee'] = $this->date_referred_committee;
                break;
            case 'Second Reading':
                $updateData['date_second_reading'] = $this->date_second_reading;
                break;
            case 'Third Reading':
                $updateData['date_third_reading'] = $this->date_third_reading;
                break;
            case 'Approved':
                $updateData['date_approved'] = $this->date_approved;
                break;
            case 'Enacted':
                $updateData['date_enacted'] = $this->date_enacted;
                $updateData['passage_mode'] = $this->passage_mode;
                break;
            case 'Vetoed':
                $updateData['date_vetoed'] = $this->date_vetoed;
                break;
            case 'Returned':
                $updateData['date_returned'] = $this->date_returned;
                break;
        }

        $this->document->update($updateData);

        AuditLogger::log("Changed status from {$oldStatus} to {$this->newStatus}", $this->document->document_id);
        $this->document->refresh()->load('statusHistory.changer');

        $this->showStatusForm = false;
        $this->statusRemarks = '';
        session()->flash('success', 'Status updated successfully.');
    }

    public function saveCommitteeReport(): void
    {
        abort_unless(Auth::user()->isAdmin() || Auth::user()->isEncoder(), 403);

        $validated = $this->validate([
            'committee_name' => ['required', Rule::in(self::STANDING_COMMITTEES)],
            'report_title' => 'required|string|max:255',
            'report_date' => 'required|date',
            'recommendation' => 'required|in:' . implode(',', self::RECOMMENDATIONS),
            'findings' => 'nullable|string',
        ]);

        $validated['document_id'] = $this->document->document_id;
        $validated['prepared_by'] = Auth::id();

        CommitteeReport::create($validated);

        $this->document->refresh()->load('committeeReports.preparer');

        $this->reset(['committee_name', 'report_title', 'findings', 'showReportForm']);
        $this->report_date = now()->format('Y-m-d');
        $this->recommendation = 'For Approval';

        session()->flash('success', 'Committee report added.');
    }

    public function saveAmendment(): void
    {
        abort_unless(Auth::user()->isAdmin(), 403);

        $validated = $this->validate([
            'section_amended' => 'required|string|max:100',
            'amendment_description' => 'required|string',
            'amendment_date' => 'required|date',
        ]);

        $validated['document_id'] = $this->document->document_id;
        $validated['amendment_status'] = 'Pending';
        $validated['submitted_by'] = Auth::id();

        Amendment::create($validated);

        $this->document->refresh()->load('amendments.submitter', 'amendments.reviewer');

        $this->reset(['section_amended', 'amendment_description', 'showAmendmentForm']);
        $this->amendment_date = now()->format('Y-m-d');

        session()->flash('success', 'Amendment submitted for review.');
    }

    public function reviewAmendment(int $amendmentId, string $decision): void
    {
        abort_unless(Auth::user()->isAdmin(), 403);

        $amendment = Amendment::findOrFail($amendmentId);
        $amendment->update([
            'amendment_status' => $decision,
            'reviewed_by' => Auth::id(),
        ]);

        $this->document->refresh()->load('amendments.submitter', 'amendments.reviewer');

        session()->flash('success', "Amendment {$decision}.");
    }

    public function saveLink(): void
    {
        abort_unless(Auth::user()->isAdmin() || Auth::user()->isEncoder(), 403);

        $validated = $this->validate([
            'link_type' => 'required|in:' . implode(',', self::LINK_TYPES),
            'reference_title' => 'required|string|max:255',
            'reference_number' => 'nullable|string|max:100',
            'source_url' => 'nullable|url|max:500',
            'referenceFile' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        if (! $validated['source_url'] && ! $this->referenceFile) {
            $this->addError('source_url', 'Provide either a URL or upload a file.');
            return;
        }

        $validated['document_id'] = $this->document->document_id;
        $validated['added_by'] = Auth::id();

        if ($this->referenceFile) {
            $validated['file_path'] = $this->referenceFile->store('document-links', 'public');
        }

        DocumentLink::create($validated);

        $this->document->refresh()->load('links.adder');

        $this->reset(['reference_title', 'reference_number', 'source_url', 'referenceFile', 'showLinkForm']);
        $this->link_type = 'Related Ordinance';

        session()->flash('success', 'Reference link added.');
    }
}