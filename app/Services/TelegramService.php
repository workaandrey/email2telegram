<?php


namespace App\Services;


use Telegram\Bot\Api;
use Telegram\Bot\BotsManager;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramService
{
    public function getBotChatId(string $token):string
    {
        $updates = $this->bot()->getUpdates();
        if(empty($updates)) {
            throw new \Exception('Please say "Hello" to your telegram bot');
        }

        /** @var Telegram\Bot\Objects\Update  $update */
        $update = array_shift($updates);

        return $update->getChat()->getId();
    }

    public function bot(): Api
    {
        return (new BotsManager(config('telegram')))->bot();
    }
}
