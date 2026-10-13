<?php

namespace App\Models;

use Database\Factories\OrdinanceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'document_id', 'ordinance_number', 'stage', 'version_number',
    'author', 'committee_referred', 'date_prepared', 'date_enacted',
    'implementing_agency', 'target_beneficiaries', 'file_path',
    'remarks', 'encoded_by',
])]
class Ordinance extends Model
{
    /** @use HasFactory<OrdinanceFactory> */
    use HasFactory;

    protected $primaryKey = 'ordinance_id';
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
