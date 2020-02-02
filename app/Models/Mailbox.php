<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Mailbox extends Model
{
    protected $fillable = ['user_id', 'name', 'host', 'port', 'encryption', 'email', 'password', 'is_active'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getPasswordAttribute($value)
    {
        return empty($value) ? $value : decrypt($value);
    }

    public function setPasswordAttribute($value)
    {
        return encrypt($value);
    }
}
