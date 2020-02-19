<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Mailbox
 * @package App\Models
 * @property-read User $user
 */
class Mailbox extends Model
{
    protected $fillable = ['user_id', 'name', 'host', 'port', 'encryption', 'email', 'password', 'is_active', 'telegram_chat_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeIsActive(Builder $builder)
    {
        return $builder->where('is_active', 1);
    }

    public function getEncryptionAttribute($value)
    {
        return trim($value);
    }
}
