<?php

namespace App\Models;

use Database\Factories\DocumentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'series_number', 'title', 'document_type', 'subject_matter',
    'sponsor', 'status', 'passage_mode', 'date_filed', 'date_calendared',
    'date_referred_committee', 'date_second_reading', 'date_third_reading',
    'date_approved', 'date_vetoed', 'date_returned',
    'date_approved_mayor', 'date_enacted', 'file_path', 'file_hash', 'encoded_by',
])]
class Document extends Model
{
    /** @use HasFactory<DocumentFactory> */
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'document_id';

    protected function casts(): array
    {
        return [
            'date_filed' => 'date',
            'date_calendared' => 'date',
            'date_referred_committee' => 'date',
            'date_second_reading' => 'date',
            'date_third_reading' => 'date',
            'date_approved' => 'date',
            'date_vetoed' => 'date',
            'date_returned' => 'date',
            'date_approved_mayor' => 'date',
            'date_enacted' => 'date',
            'deleted_at' => 'datetime',
        ];
    }


    public function encoder()
    {
        return $this->belongsTo(User::class, 'encoded_by');
    }

    public function ordinance()
    {
        return $this->hasOne(Ordinance::class, 'document_id');
    }

    public function resolution()
    {
        return $this->hasOne(Resolution::class, 'document_id');
    }

    public function committeeReports()
    {
        return $this->hasMany(CommitteeReport::class, 'document_id');
    }

    public function amendments()
    {
        return $this->hasMany(Amendment::class, 'document_id');
    }

    public function statusHistory()
    {
        return $this->hasMany(StatusHistory::class, 'document_id');
    }

    public function links()
    {
        return $this->hasMany(DocumentLink::class, 'document_id');
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class, 'document_id');
    }

    public const STATUSES = [
        'Draft', 'Filed', 'First Reading', 'For Committee Reporting',
        'Second Reading', 'Third Reading', 'Approved', 'Enacted',
        'Vetoed', 'Returned', 'Archived',
    ];

    // Resolutions don't require mayoral approval — they stop at Approved.
    public const RESOLUTION_STATUSES = [
        'Draft', 'Filed', 'First Reading', 'For Committee Reporting',
        'Second Reading', 'Third Reading', 'Approved', 'Returned', 'Archived',
    ];

    public const PASSAGE_MODES = [
        'regular' => 'Regular',
        'certified_urgent_suspended_rules' => 'Certified Urgent — Approved under Suspended Rules',
        'non_urgent_suspended_rules' => 'Non-Urgent — Approved under Suspended Rules',
    ];

    public const LOCKED_STATUSES = ['Enacted', 'Vetoed'];

    /** Statuses selectable for this document, based on its type. */
    public function availableStatuses(): array
    {
        return $this->document_type === 'Resolution' ? self::RESOLUTION_STATUSES : self::STATUSES;
    }

    /** Whether this document is finalized and locked to non-admins. */
    public function isLocked(): bool
    {
        return $this->document_type === 'Resolution'
            ? $this->status === 'Approved'
            : in_array($this->status, self::LOCKED_STATUSES);
    }
}