<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BulkEmail extends Model
{
    protected $fillable = [
        'user_id',
        'template_id',
        'template_name',
        'list_name',
        'variables',
        'emails_sent',
        'failed_count',
    ];

    protected $casts = [
        'variables' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}