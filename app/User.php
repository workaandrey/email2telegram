<?php

namespace App;

use App\Models\Mailbox;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'telegram_token', 'telegram_chat_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function mailboxes()
    {
        return $this->hasMany(Mailbox::class);
    }

    public function useTelegramBot(): void
    {
        if(empty($this->telegram_token) || empty($this->telegram_chat_id)) {
            throw new \Exception('Specify valid telegram token');
        }
        config([
            'telegram.bots.common.token' => $this->telegram_token,
            'telegram.bots.common.channel' => $this->telegram_chat_id
        ]);
    }
}
