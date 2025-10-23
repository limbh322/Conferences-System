<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id', 'recipient_id', 'type', 'notifiable_id', 'data', 'read_at'
    ];

    protected $casts = [
        'data' => 'array',  // if data is stored as JSON
        'read_at' => 'datetime',
    ];
}
