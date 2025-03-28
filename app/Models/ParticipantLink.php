<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParticipantLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'participant_id',
        'linked_user_id',
        'relation',  
    ];

    public function participant()
    {
        return $this->belongsTo(User::class, 'participant_id');
    }

    public function supportCoordinator()
    {
        return $this->belongsTo(User::class, 'support_coordinator_id');
    }

    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }
}