<?php

namespace App\Livewire\AuditLogs;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $dateFrom = '';
    public string $dateTo = '';

    public function mount(): void
    {
        abort_unless(Auth::user()->isAdmin(), 403);
    }

    public function updating($property): void
    {
        if (in_array($property, ['search', 'dateFrom', 'dateTo'])) {
            $this->resetPage();
        }
    }

    public function render()
    {
        $logs = AuditLog::query()
            ->with(['user', 'document'])
            ->when($this->search, fn ($q) => $q
                ->where('action', 'like', "%{$this->search}%")
                ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$this->search}%")))
            ->when($this->dateFrom, fn ($q) => $q->whereDate('logged_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->whereDate('logged_at', '<=', $this->dateTo))
            ->latest('logged_at')
            ->paginate(20);

        return view('livewire.audit-logs.index', [
            'logs' => $logs,
        ]);
    }
}
