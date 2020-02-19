<?php

namespace App\Jobs;

use App\Models\Mailbox;
use App\Services\TelegramService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\UploadedFile;
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

        $validateCert = $this->email->host != 'imap.gmail.com';
        $client = $this->getClient($this->email->host, $this->email->port, $this->email->encryption, $validateCert, $this->email->email, $this->email->password, 'imap', config('telegram.bots.common.channel'));
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
                    $aMessageUnseen5Days = $oFolder->query()->since(now()->subDays(5))->unseen()->get();
                    /** @var Message $oMessage */
                    foreach ($aMessageUnseen5Days as $oMessage) {
                        $emailMessage = <<<TEXT
<b>От:</b> {$oMessage->getSender()[0]->personal} ({$oMessage->getFrom()[0]->mail})
<b>Дата:</b> {$oMessage->getDate('d-M-y')}
<b>Тема: {$oMessage->getSubject()}</b>
{$oMessage->getTextBody(true)}
TEXT;
                        $this->telegramService->bot()->sendMessage([
                            'chat_id' => $chatId,
                            'parse_mode' => 'HTML',
                            'text' => $emailMessage
                        ]);
                        /** @var \Webklex\IMAP\Attachment $attachment */
                        foreach ($oMessage->getAttachments() as $attachment) {
                            $attachmentDir = sys_get_temp_dir();
                            $attachment->save($attachmentDir, $attachment->getName());
                            $attachmentPath = $attachmentDir . '/' . $attachment->getName();
                            $uploadedFile = new UploadedFile($attachmentPath, $attachment->getName(), $attachment->getMimeType());
                            $this->telegramService->bot()->sendDocument([
                                'chat_id' => $chatId,
                                'document' => $uploadedFile
                            ]);
                            unlink($attachmentPath);
                        }

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
