<div class="p-6 max-w-3xl mx-auto">
    <div class="mb-6">
        <flux:heading size="lg" class="font-serif text-ink">Backup Management</flux:heading>
        <p class="text-sm text-steel mt-1">Generate and manage database backups for disaster recovery.</p>
    </div>

    <div class="border border-gray-300 rounded-lg p-6 mb-6">
        <div class="flex items-center gap-5">
            <button
                wire:click="runBackup"
                wire:loading.attr="disabled"
                class="w-16 h-16 shrink-0 rounded-lg bg-ledger-green hover:bg-ledger-green-dark text-white flex items-center justify-center transition-colors disabled:opacity-60"
            >
                <flux:icon name="circle-stack" class="size-7" />
            </button>

            <div class="flex-1">
                <p class="text-sm font-semibold text-ink" wire:loading.remove wire:target="runBackup">
                    Create a New Backup
                </p>
                <p class="text-sm font-semibold text-steel" wire:loading wire:target="runBackup">
                    Creating backup...
                </p>
                <p class="text-xs text-steel mt-1">Automated backups also run daily via the system scheduler.</p>
            </div>
        </div>

        @if (session('success'))
            <flux:callout variant="success" class="mt-4">{{ session('success') }}</flux:callout>
        @endif
    </div>

    <p class="text-xs uppercase tracking-widest text-steel font-mono font-semibold mb-3">
        Backup History
    </p>

    <div class="border border-gray-300 rounded-lg divide-y divide-gray-200">
        @forelse ($backupHistory as $i => $backup)
            <div class="flex items-center gap-4 p-4">
                <div class="w-9 h-9 shrink-0 rounded-full bg-gray-50 border border-gray-300 text-steel text-xs font-mono font-semibold flex items-center justify-center">
                    {{ count($backupHistory) - $i }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-mono text-sm text-ink truncate">{{ $backup['name'] }}</p>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-xs text-steel">{{ $backup['date'] }}</span>
                        <span class="text-xs text-steel font-mono border border-gray-300 px-1.5 py-0.5 rounded">{{ $backup['size'] }}</span>
                    </div>
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    <button wire:click="downloadBackup('{{ $backup['name'] }}')" class="text-sm font-medium text-ink hover:text-ledger-green transition-colors">
                        Download
                    </button>
                    <span class="text-gray-300">&middot;</span>
                    <button
                        wire:click="deleteBackup('{{ $backup['name'] }}')"
                        wire:confirm="Delete this backup file permanently?"
                        class="text-sm font-medium text-red-600 hover:text-red-700 transition-colors"
                    >
                        Delete
                    </button>
                </div>
            </div>
        @empty
            <div class="p-10 text-center text-steel">
                No backups yet.
            </div>
        @endforelse
    </div>
</div>