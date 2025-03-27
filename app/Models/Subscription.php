<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'user_id',
        'list_name',
        'subscribed',
        'email',
    ];

    protected $casts = [
        'subscribed' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('subscribed', true);
    }

    public static function isUserSubscribed($userId, $list)
    {
        return self::where('user_id', $userId)
            ->where('list_name', $list)
            ->where('subscribed', true)
            ->exists();
    }
}