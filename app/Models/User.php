<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'google_id',
        'avatar',
        'password',
        'phone',
        'address',
        'city',
        'role',
        'is_admin',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function isGoogleUser(): bool
    {
        return !empty($this->google_id);
    }

    public function isStaff(): bool
    {
        return in_array($this->role, ['staff', 'admin', 'super_admin']) || $this->is_admin;
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'super_admin']) || $this->is_admin;
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin' || ($this->is_admin && empty($this->role));
    }

    public function getRoleBadgeAttribute(): array
    {
        return match ($this->role) {
            'super_admin' => ['label' => 'Super Admin', 'class' => 'bg-emerald-100 text-emerald-700 border-emerald-200'],
            'admin' => ['label' => 'Admin', 'class' => 'bg-blue-100 text-blue-700 border-blue-200'],
            'staff' => ['label' => 'Staff', 'class' => 'bg-amber-100 text-amber-700 border-amber-200'],
            default => ['label' => 'Customer', 'class' => 'bg-gray-100 text-gray-700 border-gray-200'],
        };
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return str_starts_with($this->avatar, 'http') ? $this->avatar : asset('storage/' . $this->avatar);
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=f97316&color=ffffff&bold=true';
    }
}
