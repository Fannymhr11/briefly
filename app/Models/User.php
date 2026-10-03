<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_USER = 'user';
    public const ROLE_MARKETING = 'marketing';
    public const ROLE_CREATIVE = 'creative';

    public const ROLES = [
        self::ROLE_ADMIN => 'Admin',
        self::ROLE_USER => 'User',
        self::ROLE_MARKETING => 'Marketing Communication',
        self::ROLE_CREATIVE => 'Tim Kreatif',
    ];

    protected $fillable = ['name', 'email', 'password', 'role', 'is_active'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function briefs(): HasMany
    {
        return $this->hasMany(Brief::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(ActivityHistory::class);
    }

    public function hasRole(string ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    public function getRoleLabelAttribute(): string
    {
        return self::ROLES[$this->role] ?? $this->role;
    }

    public function getInitialsAttribute(): string
    {
        $parts = preg_split('/\s+/', trim($this->name)) ?: [];
        $first = Str::upper(Str::substr($parts[0] ?? '?', 0, 1));
        $second = isset($parts[1]) ? Str::upper(Str::substr($parts[1], 0, 1)) : '';

        return $first.$second;
    }

    /** Prefix route per role: admin | user | marketing | creative */
    public function routePrefix(): string
    {
        return $this->role;
    }

    public function dashboardUrl(): string
    {
        return route($this->role.'.dashboard');
    }
}
