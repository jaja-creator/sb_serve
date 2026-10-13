<?php

namespace App\Models;

use Database\Factories\ResolutionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'document_id', 'resolution_number', 'stage', 'version_number',
    'author', 'purpose', 'date_prepared', 'date_enacted',
    'implementing_agency', 'target_beneficiaries', 'file_path',
    'remarks', 'encoded_by',
])]
class Resolution extends Model
{
    /** @use HasFactory<ResolutionFactory> */
    use HasFactory;

    protected $primaryKey = 'resolution_id';
    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'date_prepared' => 'date',
            'date_enacted' => 'date',
        ];
    }

    public function document()
    {
        return $this->belongsTo(Document::class, 'document_id');
    }

    public function encoder()
    {
        return $this->belongsTo(User::class, 'encoded_by');
    }
}
