<?php


namespace App\Telegram;

use App\User;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Laravel\Facades\Telegram;

class InfoCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = 'info';

    /**
     * @var array Command Aliases
     */
  //  protected $aliases = ['listcommands'];

    /**
     * @var string Command Description
     */
    protected $description = 'Description bot';

    /**
     * {@inheritdoc}
     */
    public function handle($arguments)
    {
        $this->replyWithChatAction(['action'=>Actions::TYPING]);
        $telegramUser=Telegram::getWebhookUpdates();
        $text=sprintf('%s: %s'.PHP_EOL, 'Ваше имя пользователя телеграм',$telegramUser["message"]["from"]['first_name']);
        $text.=sprintf('%s: %s'.PHP_EOL, 'Ваше фамилия пользователя телеграм',$telegramUser["message"]["from"]['last_name']);
        $text.=sprintf('%s'.PHP_EOL, 'Данный бот посылает всю не прочитанною почту зв 5 дней в канал телеграмма');
        $this->replyWithMessage(compact('text'));

    }
}
