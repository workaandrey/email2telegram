<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Jobs\ReadMailbox;
use App\Services\TelegramService;


/**
 * Class TelegramController
 * @package App\Http\Controllers\Backend
 */
class TelegramController extends Controller
{
    /**
     *
     */
    public $telegram;
    /**
     * @var TelegramService
     */
    private $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
    }

    /**
     *
     */
    public function webhook()
    {
        $this->telegram = $this->telegramService->bot()->getWebhookUpdate()['message'];

        $this->telegramService->bot()->commandsHandler(true);

        if (preg_match("/^.+@.+\..+$/", $this->telegram["text"])) {
            $domain = substr(strrchr($this->telegram["text"], "@"), 1);
            $res = getmxrr($domain, $mx_records, $mx_weight);
            if (!$res || !count($mx_records) || (count($mx_records) == 1 && (!$mx_records[0] || $mx_records[0] == "0.0.0.0"))) {
                $this->telegramService->bot()->sendMessage([
                    'chat_id' => $this->telegram['from']['id'],
                    'parse_mode' => 'HTML',
                    'text' => 'Wrong email'
                ]);
            } else {
                if($mailbox = Mailbox::query()->isActive()->where('email', $this->telegram["text"])->first()) {
                    ReadMailbox::dispatch($mailbox)->onQueue('read.mailbox');
                }
            }
        }

    }
}
