<div class="max-w-2xl mx-auto p-6">
    <div class="bg-white border border-gray-200 rounded-lg p-6">
        <p class="text-center text-xs uppercase tracking-widest text-steel font-mono font-semibold mb-1">
            {{ $user ? 'Edit Account' : 'New Account' }}
        </p>
        <flux:heading size="lg" class="font-serif text-center mb-6 text-ink">{{ $user ? 'Edit User Account' : 'Create User Account' }}</flux:heading>

        @if ($user && $user->id === auth()->id())
            <flux:callout variant="warning" class="mb-4">
                You are editing your own account. Role cannot be changed away from Administrator.
            </flux:callout>
        @endif

        <form wire:submit="save" class="space-y-6">
            <div>
                <p class="text-xs uppercase tracking-widest text-steel font-mono font-semibold mb-3">
                    Profile Photo
                </p>

                <div class="flex items-center gap-5">
                    <label for="photo" class="relative w-24 h-24 shrink-0 rounded-full overflow-hidden ring-4 ring-white shadow-lg shadow-gray-300/50 border border-gray-200 bg-gray-100 flex items-center justify-center cursor-pointer group">
                        @if ($photo)
                            <img src="{{ $photo->temporaryUrl() }}" alt="Preview" class="w-full h-full object-cover" />
                        @elseif ($existingPhotoPath)
                            <img src="{{ asset('storage/' . $existingPhotoPath) }}" alt="Current photo" class="w-full h-full object-cover" />
                        @else
                            <flux:icon name="camera" class="size-6 text-steel" />
                        @endif

                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-colors flex items-center justify-center">
                            <flux:icon name="camera" class="size-5 text-white opacity-0 group-hover:opacity-100 transition-opacity" />
                        </div>

                        <input id="photo" type="file" accept="image/*" wire:model="photo" class="sr-only" />
                    </label>

                    <div class="text-sm">
                        <div wire:loading wire:target="photo" class="text-xs text-steel">Uploading...</div>
                        @error('photo') <p class="text-xs text-seal-red">{{ $message }}</p> @enderror
                        @if ($photo || $existingPhotoPath)
                            <button type="button" wire:click="removePhoto" class="text-sm font-medium text-red-600 hover:text-red-700 transition-colors">
                                Remove Photo
                            </button>
                        @else
                            <p class="text-sm text-steel">Click the circle to upload a photo</p>
                            <p class="text-xs text-steel mt-0.5">JPG, PNG, or WEBP</p>
                        @endif
                    </div>
                </div>
            </div>

            <div>
                <p class="text-xs uppercase tracking-widest text-steel font-mono font-semibold mb-3">
                    Personal Details
                </p>
                <div class="space-y-4">
                    <flux:input wire:model="name" label="Full Name" class="!text-ink" />
                    <flux:input wire:model="username" label="Username" class="!text-ink" />
                    <flux:input wire:model="email" label="Email" type="email" class="!text-ink" />
                </div>
            </div>

            <div>
                <p class="text-xs uppercase tracking-widest text-steel font-mono font-semibold mb-3">
                    Access
                </p>
                <div class="space-y-4">
                    <flux:select wire:model="role" label="Role" :disabled="$user && $user->id === auth()->id()" class="!text-ink">
                        <flux:select.option value="administrator">Administrator</flux:select.option>
                        <flux:select.option value="encoder">Encoder (Legislative Staff)</flux:select.option>
                        <flux:select.option value="viewer">Viewer (SB Member)</flux:select.option>
                    </flux:select>

                    <div class="flex items-center justify-between bg-gray-50 rounded-lg px-4 py-3">
                        <div>
                            <p class="text-sm font-medium text-ink">Active Account</p>
                            <p class="text-xs text-steel">Account can log in when enabled</p>
                        </div>
                        <label class="mini-lever {{ $is_active ? 'mini-lever-on' : '' }} {{ ($user && $user->id === auth()->id()) ? 'opacity-50 pointer-events-none' : '' }}">
                            <input type="checkbox" wire:model="is_active" class="sr-only" @disabled($user && $user->id === auth()->id())>
                            <div class="mini-lever-knob"></div>
                        </label>
                    </div>
                </div>
            </div>

            <div>
                <p class="text-xs uppercase tracking-widest text-steel font-mono font-semibold mb-3">
                    Credentials
                </p>
                <div class="space-y-4">
                    <flux:input wire:model="password" label="{{ $user ? 'New Password (leave blank to keep current)' : 'Password' }}" type="password" class="!text-ink" />
                    <flux:input wire:model="password_confirmation" label="Confirm Password" type="password" class="!text-ink" />
                </div>
            </div>

            <div class="flex gap-2 pt-4 border-t border-gray-200">
                <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="save">
                    <span wire:loading.remove wire:target="save">Save Account</span>
                    <span wire:loading wire:target="save">Saving...</span>
                </flux:button>
                <flux:button href="{{ route('users.index') }}" variant="ghost" class="!text-ink">Cancel</flux:button>
            </div>
        </form>
    </div>
</div>