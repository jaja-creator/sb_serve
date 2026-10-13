<div class="p-6 max-w-3xl mx-auto">
    <div class="mb-6">
        <flux:heading size="lg" class="font-serif text-ink">System Settings</flux:heading>
        <p class="text-sm text-steel mt-1">Manage your organization's configuration</p>
    </div>

    @if (session('success'))
        <flux:callout variant="success" class="mb-4">{{ session('success') }}</flux:callout>
    @endif

    <div class="border border-gray-300 rounded-lg p-6 mb-6">
        <p class="text-xs uppercase tracking-widest text-steel font-mono font-semibold mb-4">
            Organization Details
        </p>

        <form wire:submit="save" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <flux:input wire:model="organization_name" label="Organization Name" class="!text-ink [&_input]:border [&_input]:border-gray-300" />
                <flux:input wire:model="municipality" label="Municipality" class="!text-ink [&_input]:border [&_input]:border-gray-300" />
                <flux:input wire:model="contact_email" label="Contact Email (optional)" type="email" class="!text-ink [&_input]:border [&_input]:border-gray-300" />
                <flux:input wire:model="documents_per_page" label="Documents Per Page (listings)" type="number" min="5" max="100" class="!text-ink [&_input]:border [&_input]:border-gray-300" />
                <flux:input wire:model="backup_retention_count" label="Backup Retention (number of backups to keep)" type="number" min="1" max="100" class="!text-ink [&_input]:border [&_input]:border-gray-300" />
            </div>

            <flux:button type="submit" variant="primary">Save Settings</flux:button>
        </form>
    </div>

    <div class="border border-gray-300 rounded-lg p-6">
        <p class="text-xs uppercase tracking-widest text-steel font-mono font-semibold mb-1">
            Maintenance Mode
        </p>
        <p class="text-sm text-steel mb-4">
            When enabled, the system becomes inaccessible to all users except administrators.
        </p>

        <div class="flex items-center justify-between bg-gray-50 rounded-lg px-4 py-3">
            <span class="text-sm font-medium {{ $maintenance_mode ? 'text-red-600' : 'text-ink' }}">
                {{ $maintenance_mode ? 'System is offline' : 'System is online' }}
            </span>

            <button
                wire:click="toggleMaintenance"
                wire:confirm="{{ $maintenance_mode ? 'Bring the system back online?' : 'Take the system offline for maintenance? Only administrators will be able to access it.' }}"
                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors {{ $maintenance_mode ? 'bg-red-600' : 'bg-gray-300' }}"
            >
                <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $maintenance_mode ? 'translate-x-6' : 'translate-x-1' }}"></span>
            </button>
        </div>
    </div>
</div>