<?php

namespace App\Jobs;

use App\Models\Mailbox;
use App\Services\TelegramService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Webklex\IMAP\Client;
use Webklex\IMAP\Exceptions\ConnectionFailedException;
use Webklex\IMAP\Exceptions\MailboxFetchingException;
use Webklex\IMAP\Exceptions\MaskNotFoundException;
use Webklex\IMAP\Folder;
use Webklex\IMAP\Message;

class ReadMailbox implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var Mailbox
     */
    private $email;
    /**
     * @var TelegramService
     */
    private $telegramService;

    /**
     * ReadMailbox constructor.
     * @param Mailbox $email
     * @param TelegramService $telegramService
     * @throws \Exception
     */
    public function __construct(Mailbox $email)
    {
        $this->email = $email;
    }

    /**
     * @param TelegramService $telegramService
     */
    public function handle(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
        config([
            'telegram.bots.common.token' => $this->email->user->telegram_token,
            'telegram.bots.common.channel' => $this->email->user->telegram_chat_id
        ]);

        $client = $this->getClient($this->email->host, $this->email->port, $this->email->encryption, true, $this->email->email, $this->email->password, 'imap', config('telegram.bots.common.channel'));
        $connect = $this->getConnect($client, config('telegram.bots.common.channel'));
        $this->sendFolderInbox($connect, config('telegram.bots.common.channel'));
    }

    /**
     * @param $host
     * @param $port
     * @param $encryption
     * @param $validate_cert
     * @param $username
     * @param $password
     * @param $protocol
     * @param $chatId
     * @return Client
     */
    public function getClient($host, $port, $encryption, $validate_cert, $username, $password, $protocol, $chatId)
    {
        try {
            $oClient = new Client([
                'host' => $host,
                'port' => $port,
                'encryption' => trim($encryption == 'none' ? false : $encryption),
                'validate_cert' => $validate_cert,
                'username' => $username,
                'password' => $password,
                'protocol' => $protocol
            ]);
            return $oClient;
        } catch (MaskNotFoundException $e) {
            $this->telegramService->bot()->sendMessage([
                'chat_id' => $chatId,
                'parse_mode' => 'HTML',
                'text' => $e->getMessage()
            ]);

        }

    }

    /**
     * @param $oClient
     * @param $chatId
     * @return bool
     */
    public function getConnect($oClient, $chatId)
    {
        try {
            $oClient->connect();
            return $oClient;
        } catch (ConnectionFailedException $e) {
            $this->telegramService->bot()->sendMessage([
                'chat_id' => $chatId,
                'parse_mode' => 'HTML',
                'text' => $e->getMessage()
            ]);
        }

    }

    /**
     * @param $oClient
     * @param $chatId
     */
    public function sendFolderInbox($oClient, $chatId)
    {
        try {
            $aFolder = $oClient->getFolders();
            /** @var Folder $oFolder */
            foreach ($aFolder as $oFolder) {

                if ($oFolder->name == 'INBOX') {
                    /*$text = sprintf('%s: %s' . PHP_EOL, "Название папки", $oFolder->name);*/
                    $aMessageUnseen5Days = $oFolder->query()->since(now()->subDays(5))->unseen()->get();
                    /*$text .= sprintf('%s: %s' . PHP_EOL, "Количество присланных не прочитанных сообщений", $oFolder->search()->unseen()->leaveUnread()->setFetchBody(false)->setFetchAttachment(false)->since(now()->subDays(10))->get()->count());
                    $this->telegramService->bot()->sendMessage([
                        'chat_id' => $chatId,
                        'parse_mode' => 'HTML',
                        'text' => $text
                    ]);*/
                    /** @var Message $oMessage */
                    foreach ($aMessageUnseen5Days as $oMessage) {
                        $post = sprintf('%s: %s' . PHP_EOL, "Тема ", $oMessage->getSubject());
                        $post .= sprintf('%s: %s' . PHP_EOL, "Отправитель", $oMessage->getSender()[0]->personal);
                        $post .= sprintf('%s: %s' . PHP_EOL, "Дата отправления", $oMessage->getDate('d-M-y'));
                        $post .= sprintf('%s: %s' . PHP_EOL, "Почта отправителя", $oMessage->getFrom()[0]->mail);
                        $post .= sprintf('%s: %s' . PHP_EOL, "Количество вложенний в почте", $oMessage->getAttachments()->count() > 0 ? 'yes' : 'no');
                        $post .= sprintf('%s: %s' . PHP_EOL, "Само письмо", $oMessage->getTextBody(true));
                        $this->telegramService->bot()->sendMessage([
                            'chat_id' => $chatId,
                            'parse_mode' => 'HTML',
                            'text' => $post
                        ]);

                        $oMessage->setFlag('SEEN');
                        $oClient->expunge();
                    }
                }
            }

        } catch (ConnectionFailedException $e) {
            $this->telegramService->bot()->sendMessage([
                'chat_id' => $chatId,
                'parse_mode' => 'HTML',
                'text' => $e->getMessage()
            ]);

        } catch (MailboxFetchingException $e) {
            $this->telegramService->bot()->sendMessage([
                'chat_id' => $chatId,
                'parse_mode' => 'HTML',
                'text' => $e->getMessage() . 'lkll'
            ]);
        }
    }
}
