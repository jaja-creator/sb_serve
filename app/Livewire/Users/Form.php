<?php

namespace App\Livewire\Users;

use App\Models\User;
use App\Support\AuditLogger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class Form extends Component
{
    use WithFileUploads;

    public ?User $user = null;

    public string $name = '';
    public string $username = '';
    public string $email = '';
    public string $role = 'encoder';
    public bool $is_active = true;
    public string $password = '';
    public string $password_confirmation = '';

    public ?string $existingPhotoPath = null;

    /** @var \Livewire\Features\SupportFileUploads\TemporaryUploadedFile|null */
    public $photo = null;

    public const ROLES = ['administrator', 'encoder', 'viewer'];

    public function mount(?User $user = null): void
    {
        abort_unless(Auth::user()->isAdmin(), 403);

        $this->password = '';
        $this->password_confirmation = '';

        if ($user?->exists) {
            $this->user = $user;
            $this->name = $user->name;
            $this->username = $user->username;
            $this->email = $user->email;
            $this->role = $user->role;
            $this->is_active = $user->is_active;
            $this->existingPhotoPath = $user->profile_photo_path;
        }
    }

    public function removePhoto(): void
    {
        $this->existingPhotoPath = null;
    }

    public function save(): void
    {
        abort_unless(Auth::user()->isAdmin(), 403);

        // Prevent an admin from demoting themselves out of the admin role
        if ($this->user && $this->user->id === Auth::id() && $this->role !== 'administrator') {
            $this->addError('role', 'You cannot change your own role away from Administrator.');
            return;
        }

        // Prevent demoting the last remaining administrator
        if ($this->user && $this->user->role === 'administrator' && $this->role !== 'administrator') {
            $remainingAdmins = User::where('role', 'administrator')
                ->where('id', '!=', $this->user->id)
                ->count();

            if ($remainingAdmins === 0) {
                $this->addError('role', 'Cannot change role: this is the only remaining Administrator account.');
                return;
            }
        }

        $rules = [
            'name' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:50', Rule::unique('users', 'username')->ignore($this->user?->id)],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($this->user?->id)],
            'role' => 'required|in:' . implode(',', self::ROLES),
            'is_active' => 'boolean',
            'photo' => 'nullable|image|max:2048',
        ];

        if (! $this->user) {
            $rules['password'] = 'required|string|min:8|confirmed';
        } elseif ($this->password) {
            $rules['password'] = 'string|min:8|confirmed';
        }

        $validated = $this->validate($rules);

        unset($validated['photo']);

        if ($this->photo) {
            $validated['profile_photo_path'] = $this->photo->store('user-photos', 'public');
        } elseif ($this->user && $this->existingPhotoPath === null) {
            $validated['profile_photo_path'] = null;
        }

        if ($this->user) {
            if (empty($this->password)) {
                unset($validated['password']);
            } else {
                $validated['password'] = Hash::make($this->password);
            }
            unset($validated['password_confirmation']);

            $this->user->update($validated);
            AuditLogger::log("Updated user account: {$this->user->username}");
            session()->flash('success', 'User account updated.');
        } else {
            $validated['password'] = Hash::make($this->password);
            unset($validated['password_confirmation']);

            $newUser = User::create($validated);
            AuditLogger::log("Created user account: {$newUser->username} ({$newUser->role})");
            session()->flash('success', 'User account created.');
        }

        $this->redirect(route('users.index'), navigate: true);
    }
}