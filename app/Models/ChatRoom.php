<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatRoom extends Model
{
    protected $fillable = [
        'type',         // 'private' or 'group'
        'name',         // optional for group chats
        'participants', // JSON array of user IDs
    ];

    protected $casts = [
        'participants' => 'array',
    ];
}
