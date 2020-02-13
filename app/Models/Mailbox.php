<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Mailbox extends Model
{
    protected $fillable = ['user_id', 'name', 'host', 'port', 'encryption', 'email', 'password', 'is_active', 'telegram_chat_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
