<?php

namespace App\Models;

use Database\Factories\DocumentLinkFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'document_id', 'link_type', 'reference_title',
    'reference_number', 'source_url', 'file_path', 'added_by',
])]
class DocumentLink extends Model
{
    /** @use HasFactory<DocumentLinkFactory> */
    use HasFactory;

    protected $primaryKey = 'link_id';
    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'added_at' => 'datetime',
        ];
    }

    public function document()
    {
        return $this->belongsTo(Document::class, 'document_id');
    }

    public function adder()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}
