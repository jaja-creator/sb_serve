<?php

namespace App\Models;

use Database\Factories\CommitteeReportFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'document_id', 'committee_name', 'report_title', 'report_date',
    'recommendation', 'findings', 'file_path', 'prepared_by',
])]
class CommitteeReport extends Model
{
    /** @use HasFactory<CommitteeReportFactory> */
    use HasFactory;

    protected $primaryKey = 'report_id';
    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'report_date' => 'date',
        ];
    }

    public function document()
    {
        return $this->belongsTo(Document::class, 'document_id');
    }

    public function preparer()
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }
}
