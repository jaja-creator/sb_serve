<?php

namespace App\Livewire;

use App\Models\Amendment;
use App\Models\AuditLog;
use App\Models\Document;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $totalDocuments = Document::count();
        $totalOrdinances = Document::where('document_type', 'Ordinance')->count();
        $totalResolutions = Document::where('document_type', 'Resolution')->count();

        $statusBreakdown = Document::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $pendingAmendments = Amendment::where('amendment_status', 'Pending')->count();

        $recentDocuments = Document::with('encoder')
            ->latest('date_filed')
            ->limit(5)
            ->get();

        $recentActivity = AuditLog::with(['user', 'document'])
            ->latest('logged_at')
            ->limit(8)
            ->get();

        return view('livewire.dashboard', [
            'totalDocuments' => $totalDocuments,
            'totalOrdinances' => $totalOrdinances,
            'totalResolutions' => $totalResolutions,
            'statusBreakdown' => $statusBreakdown,
            'pendingAmendments' => $pendingAmendments,
            'recentDocuments' => $recentDocuments,
            'recentActivity' => $recentActivity,
        ]);
    }
}
