<?php

namespace App\Livewire;

use App\Models\SystemSetting;
use App\Support\AuditLogger;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Livewire\Component;

class Backup extends Component
{
    public array $backupHistory = [];

    public function mount(): void
    {
        abort_unless(Auth::user()->isAdmin(), 403);
        $this->loadHistory();
    }

    protected function backupPath(): string
    {
        $path = storage_path('app/backups');
        if (! File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }
        return $path;
    }

    protected function loadHistory(): void
    {
        $files = collect(File::files($this->backupPath()))
            ->sortByDesc(fn ($file) => $file->getMTime())
            ->map(fn ($file) => [
                'name' => $file->getFilename(),
                'size' => round($file->getSize() / 1024, 1) . ' KB',
                'date' => date('M d, Y g:i A', $file->getMTime()),
            ])
            ->values()
            ->all();

        $this->backupHistory = $files;
    }

    protected function pruneOldBackups(): void
    {
        $retentionCount = (int) SystemSetting::get('backup_retention_count', 10);

        $files = collect(File::files($this->backupPath()))
            ->sortByDesc(fn ($file) => $file->getMTime())
            ->values();

        if ($files->count() <= $retentionCount) {
            return;
        }

        $toDelete = $files->slice($retentionCount);

        foreach ($toDelete as $file) {
            File::delete($file->getPathname());
            AuditLogger::log("Auto-pruned backup file (retention limit): {$file->getFilename()}");
        }
    }

    public function runBackup(): void
    {
        abort_unless(Auth::user()->isAdmin(), 403);

        Artisan::call('backup:run');
        $this->pruneOldBackups();

        session()->flash('success', 'Backup completed successfully.');
        $this->loadHistory();
    }

    public function downloadBackup(string $filename)
    {
        abort_unless(Auth::user()->isAdmin(), 403);

        $filepath = $this->backupPath() . DIRECTORY_SEPARATOR . basename($filename);

        abort_unless(File::exists($filepath), 404);

        return response()->download($filepath);
    }

    public function deleteBackup(string $filename): void
    {
        abort_unless(Auth::user()->isAdmin(), 403);

        $filepath = $this->backupPath() . DIRECTORY_SEPARATOR . basename($filename);

        if (File::exists($filepath)) {
            File::delete($filepath);
            AuditLogger::log("Deleted backup file: {$filename}");
            session()->flash('success', 'Backup file deleted.');
        }

        $this->loadHistory();
    }

    public function render()
    {
        return view('livewire.backup');
    }
}