<?php

namespace App\Livewire\Users;

use App\Models\User;
use App\Support\AuditLogger;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $roleFilter = '';
    public string $statusFilter = '';

    public function mount(): void
    {
        abort_unless(Auth::user()->isAdmin(), 403);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingRoleFilter(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function toggleActive(int $userId): void
    {
        abort_unless(Auth::user()->isAdmin(), 403);

        $user = User::findOrFail($userId);

        if ($userId === Auth::id()) {
            session()->flash('error', 'You cannot deactivate your own account.');
            return;
        }

        if ($user->is_active && $user->role === 'administrator') {
            $remainingActiveAdmins = User::where('role', 'administrator')
                ->where('is_active', true)
                ->where('id', '!=', $userId)
                ->count();

            if ($remainingActiveAdmins === 0) {
                session()->flash('error', 'Cannot deactivate: this is the only active Administrator account.');
                return;
            }
        }

        $user->update(['is_active' => ! $user->is_active]);
        AuditLogger::log(($user->is_active ? 'Activated' : 'Deactivated') . " user account: {$user->username}");
        session()->flash('success', $user->is_active ? 'Account activated.' : 'Account deactivated.');
    }

    public function render()
    {
        $users = User::query()
            ->when($this->search, fn ($query) => $query
                ->where('name', 'like', "%{$this->search}%")
                ->orWhere('username', 'like', "%{$this->search}%")
                ->orWhere('email', 'like', "%{$this->search}%"))
            ->when($this->roleFilter, fn ($query) => $query->where('role', $this->roleFilter))
            ->when($this->statusFilter !== '', fn ($query) => $query->where('is_active', $this->statusFilter === 'active'))
            ->latest()
            ->paginate(10);

        return view('livewire.users.index', [
            'users' => $users,
        ]);
    }
}
