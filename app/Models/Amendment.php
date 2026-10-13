<?php

namespace App\Models;

use Database\Factories\AmendmentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'document_id', 'section_amended', 'amendment_description',
    'amendment_date', 'amendment_status', 'submitted_by', 'reviewed_by',
])]
class Amendment extends Model
{
    /** @use HasFactory<AmendmentFactory> */
    use HasFactory;

    protected $primaryKey = 'amendment_id';
    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'amendment_date' => 'date',
        ];
    }

    public function document()
    {
        return $this->belongsTo(Document::class, 'document_id');
    }

    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
