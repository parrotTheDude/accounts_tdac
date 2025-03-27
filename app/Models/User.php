<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    const ROLES = [
        'master' => 'Master Admin',
        'superadmin' => 'Super Admin',
        'admin' => 'Administrator',
        'staff' => 'Staff',
        'participant' => 'Participant',
        'parent' => 'Parent',
        'external' => 'External',
    ];
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'last_name',
        'email',
        'password',
        'user_type',
        'gender',
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
        ];
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function getFullNameAttribute()
    {
        return "{$this->name} {$this->last_name}";
    }

    public function getRoleNameAttribute()
    {
        return self::ROLES[$this->user_type] ?? 'Unknown';
    }

    public function isAdmin()
    {
        return in_array($this->user_type, ['admin', 'master', 'superadmin']);
    }
}
