<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, HasUuids;

    protected $fillable = [
        'uuid',
        'full_name',
        'email',
        'phone',
        'password',
        'role',
        'avatar_id',
        'provider',
        'provider_id',
        'is_active',
        'last_login_at',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'customer_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /** Ảnh đại diện user từ media */
    public function avatar()
    {
        return $this->belongsTo(Media::class, 'avatar_id');
    }
}
