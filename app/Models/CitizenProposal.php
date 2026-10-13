<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'proposal_type', 'title', 'description', 'proposer_name',
    'proposer_email', 'proposer_phone', 'proposer_barangay',
    'status', 'admin_remarks', 'reviewed_by', 'reviewed_at',
])]
class CitizenProposal extends Model
{
    use HasFactory;

    protected $primaryKey = 'proposal_id';

    protected function casts(): array
    {
        return [
            'reviewed_at' => 'datetime',
        ];
    }

    public const TYPES = ['Ordinance', 'Resolution'];

    public const STATUSES = ['Pending', 'Under Review', 'Forwarded to SB', 'Declined'];

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
