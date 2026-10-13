<?php

namespace App\Livewire;

use App\Models\SystemSetting;
use App\Support\AuditLogger;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Settings extends Component
{
    public string $organization_name = '';
    public string $municipality = '';
    public string $contact_email = '';
    public int $documents_per_page = 10;
    public int $backup_retention_count = 10;
    public bool $maintenance_mode = false;

    public function mount(): void
    {
        abort_unless(Auth::user()->isAdmin(), 403);

        $this->organization_name = SystemSetting::get('organization_name', 'Sangguniang Bayan of Allacapan');
        $this->municipality = SystemSetting::get('municipality', 'Allacapan, Cagayan');
        $this->contact_email = SystemSetting::get('contact_email', '');
        $this->documents_per_page = (int) SystemSetting::get('documents_per_page', 10);
        $this->backup_retention_count = (int) SystemSetting::get('backup_retention_count', 10);
        $this->maintenance_mode = app()->isDownForMaintenance();
    }

    public function save(): void
    {
        abort_unless(Auth::user()->isAdmin(), 403);

        $validated = $this->validate([
            'organization_name' => 'required|string|max:255',
            'municipality' => 'required|string|max:255',
            'contact_email' => 'nullable|email',
            'documents_per_page' => 'required|integer|min:5|max:100',
            'backup_retention_count' => 'required|integer|min:1|max:100',
        ]);

        foreach ($validated as $key => $value) {
            SystemSetting::set($key, (string) $value);
        }

        AuditLogger::log('Updated system settings');
        session()->flash('success', 'Settings saved successfully.');
    }

    public function toggleMaintenance(): void
    {
        abort_unless(Auth::user()->isAdmin(), 403);

        if ($this->maintenance_mode) {
            Artisan::call('up');
            AuditLogger::log('Disabled maintenance mode');
            session()->flash('success', 'Maintenance mode disabled. The site is now live.');
        } else {
            Artisan::call('down', ['--secret' => 'admin-bypass']);
            AuditLogger::log('Enabled maintenance mode');
            session()->flash('success', 'Maintenance mode enabled.');
        }

        $this->maintenance_mode = app()->isDownForMaintenance();
    }

    public function render()
    {
        return view('livewire.settings');
    }
}