<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class VerificationToken extends Model
{
    protected $fillable = ['user_id', 'token', 'expires_at'];

    public $timestamps = false;

    protected $dates = ['expires_at'];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }
}