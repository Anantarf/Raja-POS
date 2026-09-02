<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role_id',
        'status',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function hasRole(string $roleName): bool
    {
        return $this->role && strtoupper($this->role->name) === strtoupper($roleName);
    }

    public function hasPermission(string $permissionName): bool
    {
        if ($this->hasRole('OWNER')) {
            return true;
        }

        if (!$this->role) {
            return false;
        }

        return $this->role->permissions()->where('name', $permissionName)->exists();
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->status === 'ACTIVE';
    }
}
