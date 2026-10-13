<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <flux:heading size="lg" class="font-serif text-ink">User Accounts</flux:heading>
            <p class="text-sm text-steel mt-1">{{ $users->total() }} account(s) &middot; Sangguniang Bayan of Allacapan</p>
        </div>
        <flux:button href="{{ route('users.create') }}" variant="primary" icon="plus">
            Create Account
        </flux:button>
    </div>

    @if (session('success'))
        <flux:callout variant="success" class="mb-4">{{ session('success') }}</flux:callout>
    @endif

    @if (session('error'))
        <flux:callout variant="danger" class="mb-4">{{ session('error') }}</flux:callout>
    @endif

    <div class="border border-gray-300 rounded-lg p-5 mb-6 flex flex-wrap gap-3">
        <flux:input wire:model.live.debounce.400ms="search" placeholder="Search by name, username, or email..." icon="magnifying-glass" class="flex-1 min-w-[220px] !text-ink [&_input]:border [&_input]:border-gray-300 [&_input]:px-3 [&_input]:py-2" />

        <flux:select wire:model.live="roleFilter" placeholder="All Roles" class="w-full sm:w-44 !text-ink [&_button]:border [&_button]:border-gray-300 [&_button]:px-3 [&_button]:py-2">
            <flux:select.option value="">All Roles</flux:select.option>
            <flux:select.option value="administrator">Administrator</flux:select.option>
            <flux:select.option value="encoder">Encoder</flux:select.option>
            <flux:select.option value="viewer">Viewer</flux:select.option>
        </flux:select>

        <flux:select wire:model.live="statusFilter" placeholder="All Statuses" class="w-full sm:w-40 !text-ink [&_button]:border [&_button]:border-gray-300 [&_button]:px-3 [&_button]:py-2">
            <flux:select.option value="">All Statuses</flux:select.option>
            <flux:select.option value="active">Active</flux:select.option>
            <flux:select.option value="inactive">Inactive</flux:select.option>
        </flux:select>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse ($users as $user)
            @php
                $roleStyles = match($user->role) {
                    'administrator' => 'bg-red-50 text-red-700 ring-1 ring-inset ring-red-200',
                    'encoder' => 'bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-200',
                    default => 'bg-gray-100 text-gray-700 ring-1 ring-inset ring-gray-200',
                };
            @endphp
            <div class="bg-white border border-gray-200 rounded-lg p-5 hover:shadow-md hover:border-gray-300 transition-all {{ !$user->is_active ? 'opacity-60' : '' }}" wire:key="user-{{ $user->id }}">
                <div class="flex flex-col items-center text-center mb-4">
                    <div class="w-24 h-24 shrink-0 rounded-full overflow-hidden ring-4 ring-white shadow-lg shadow-gray-300/50 border border-gray-200 bg-gray-100 flex items-center justify-center mb-3">
                        @if ($user->photoUrl())
                            <img src="{{ $user->photoUrl() }}" alt="{{ $user->name }}" class="w-full h-full object-cover" />
                        @else
                            <flux:avatar :initials="$user->initials()" class="!w-full !h-full !text-2xl" />
                        @endif
                    </div>

                    <p class="font-serif font-semibold text-ink leading-tight">{{ $user->name }}</p>
                    <p class="text-xs text-steel font-mono">{{ $user->username }}</p>

                    @if ($user->id === auth()->id())
                        <span class="text-[10px] uppercase tracking-wide font-semibold text-ledger-green mt-1">You</span>
                    @endif
                </div>

                <div class="flex flex-wrap justify-center gap-1.5 mb-3">
                    <span class="text-[11px] font-semibold uppercase tracking-wide px-2 py-0.5 rounded-full {{ $roleStyles }}">
                        {{ ucfirst($user->role) }}
                    </span>
                    <span class="text-[11px] font-semibold uppercase tracking-wide px-2 py-0.5 rounded-full {{ $user->is_active ? 'bg-green-50 text-green-700 ring-1 ring-inset ring-green-200' : 'bg-gray-100 text-gray-500 ring-1 ring-inset ring-gray-200' }}">
                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>

                <p class="text-sm text-steel text-center truncate mb-4">{{ $user->email }}</p>

                <div class="flex items-center justify-center gap-2 pt-3 border-t border-gray-100">
                    <a href="{{ route('users.edit', $user->id) }}" wire:navigate class="text-sm font-medium text-ink hover:text-ledger-green transition-colors">
                        Edit
                    </a>

                    @if ($user->id !== auth()->id())
                        <span class="text-gray-300">&middot;</span>
                        <button
                            wire:click="toggleActive({{ $user->id }})"
                            wire:confirm="Are you sure you want to {{ $user->is_active ? 'deactivate' : 'activate' }} this account?"
                            class="text-sm font-medium {{ $user->is_active ? 'text-red-600 hover:text-red-700' : 'text-ledger-green hover:text-ledger-green-dark' }} transition-colors"
                        >
                            {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white border border-gray-200 rounded-lg p-10 text-center text-steel">
                No user accounts found.
            </div>
        @endforelse
    </div>

    <div class="mt-6">{{ $users->links() }}</div>
</div>