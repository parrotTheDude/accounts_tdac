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

    // Rank for permission comparison
    const ROLE_RANKS = [
        'master' => 3,
        'superadmin' => 2,
        'admin' => 1,
        'staff' => 0,
        'participant' => -1,
        'parent' => -2,
        'external' => -3,
    ];

    public function roleBadge()
    {
        return match($this->user_type) {
            'parent' => 'bg-green-100 text-green-700',
            'support_coordinator' => 'bg-blue-100 text-blue-700',
            'external' => 'bg-gray-200 text-gray-700',
            'participant' => 'bg-purple-100 text-purple-700',
            default => 'bg-gray-200 text-gray-700'
        };
    }

    const ADMIN_ROLES = ['admin', 'superadmin', 'master'];

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
        'archived_at',
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
        return in_array($this->user_type, self::ADMIN_ROLES);
    }

    public function canEdit(User $other): bool
    {
        $ranks = self::ROLE_RANKS;

        return isset($ranks[$this->user_type], $ranks[$other->user_type]) &&
            $ranks[$this->user_type] >= $ranks[$other->user_type];
    }

    public function isOneOf(array $roles): bool
    {
        return in_array($this->user_type, $roles);
    }

    public function rank(): int
    {
        return self::ROLE_RANKS[$this->user_type] ?? -99;
    }

    public function getAvailableRoles()
    {
        $currentRank = self::ROLE_RANKS[$this->user_type] ?? -999;

        return collect(self::ROLES)->filter(function ($label, $role) use ($currentRank) {
            return self::ROLE_RANKS[$role] <= $currentRank;
        });
    }

    public function verificationToken()
    {
        return $this->hasOne(VerificationToken::class);
    }

    public function markEmailAsVerified()
    {
        $this->email_verified_at = now();
        return $this->save();
    }

    // User.php
    public function isArchived()
    {
        return !is_null($this->archived_at);
    }

    // All links where this user is the participant
    public function participantLinks()
    {
        return $this->hasMany(ParticipantLink::class, 'participant_id');
    }

    // All links where this user is the linked user (could be parent or support coordinator)
    public function linkedParticipantLinks()
    {
        return $this->hasMany(ParticipantLink::class, 'linked_user_id');
    }

    // Easy access: Get parents
    public function parentLinks()
    {
        return $this->hasMany(ParticipantLink::class, 'participant_id')->where('relation', 'parent')->with('relatedUser');
    }

    // Easy access: Get support coordinators
    public function supportCoordinatorLinks()
    {
        return $this->hasMany(ParticipantLink::class, 'participant_id')->where('relation', 'support_coordinator')->with('relatedUser');
    }

    // Participants this support coordinator is linked to
    public function coordinatedParticipants()
    {
        return User::whereIn('id', ParticipantLink::where('support_coordinator_id', $this->id)->pluck('participant_id'))->get();
    }

    // The parents of this participant
    public function parents()
    {
        return User::whereIn('id', ParticipantLink::where('participant_id', $this->id)->pluck('parent_id'))->get();
    }

    // The support coordinator of this participant
    public function supportCoordinator()
    {
        return User::find(
            ParticipantLink::where('participant_id', $this->id)->value('support_coordinator_id')
        );
    }

    public function linkedParticipants()
    {
        return $this->belongsToMany(User::class, 'participant_links', 'linked_user_id', 'participant_id');
    }
}
