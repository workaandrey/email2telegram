<?php

namespace App\Console\Commands;

use App\Models\Mailbox;
use Illuminate\Console\Command;
use Telegram\Bot\Laravel\Facades\Telegram;
use Webklex\IMAP\Client;
use Webklex\IMAP\Exceptions\ConnectionFailedException;
use Webklex\IMAP\Exceptions\MailboxFetchingException;
use Webklex\IMAP\Exceptions\MaskNotFoundException;

/**
 * Class PostEmailToTelegram
 * @package App\Console\Commands
 */
class PostEmailToTelegram extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'postEmailToTelegram:5';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description post email to telegram 5 days';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {    $mailboxes=  Mailbox::all();
        foreach ($mailboxes as $email) {
            $client=$this->getClient($email->host, $email->port, $email->email, $email->password,config('telegram.bots.common.channel'));
            $connect = $this->getConnect($client, config('telegram.bots.common.channel'));
            $this->sendFolderInbox($connect, config('telegram.bots.common.channel'));
        }
    }

    /**
     * @param $host
     * @param $port
     * @param $username
     * @param $password
     * @param $chatId
     * @return Client
     */
    public function getClient($host, $port,  $username, $password,  $chatId){
        try {
            $oClient = new Client([
                'host' => $host,
                'port' => $port,
                'encryption' => 'ssl',
                'validate_cert' => false,
                'username' => $username,
                'password' => $password,
                'protocol' => 'imap'
            ]);
            return $oClient ;
        } catch (MaskNotFoundException $e) {
            Telegram::sendMessage([
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
    public function getConnect($oClient , $chatId){
        try {
            $oClient->connect();
            return $oClient;
        } catch (ConnectionFailedException $e) {
            Telegram::sendMessage([
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
    public function sendFolderInbox($oClient, $chatId){
        try {
            $aFolder = $oClient->getFolders();
            foreach($aFolder as $oFolder) {

                if ($oFolder->name == 'INBOX') {
                    $text=sprintf('%s: %s'.PHP_EOL,"Название папки",$oFolder->name);
                    $aMessageUnseen5Days = $oFolder->query()->since(now()->subDays(5))->get();
                    $text.=sprintf('%s: %s'.PHP_EOL,"Количество присланных не прочитанных сообщений",$oFolder->search()->unseen()->leaveUnread()->setFetchBody(false)->setFetchAttachment(false)->since(now()->subDays(5))->get()->count());
                    Telegram::sendMessage([
                        'chat_id' => $chatId,
                        'parse_mode' => 'HTML',
                        'text' =>$text
                    ]);
                    foreach ($aMessageUnseen5Days as $oMessage) {
                        $text=sprintf('%s: %s'.PHP_EOL,"Ваш идентификатор пользователя",$oMessage->getUid());
                        $text.=sprintf('%s: %s'.PHP_EOL,"Тема ",$oMessage->getSubject());
                        $text.=sprintf('%s: %s'.PHP_EOL,"Дата отправления",$oMessage->getDate('d-M-y'));
                        Telegram::sendMessage([
                            'chat_id' => $chatId,
                            'parse_mode' => 'HTML',
                            'text' =>$text
                        ]);
                    }
                }
            }

        } catch (ConnectionFailedException $e) {
            Telegram::sendMessage([
                'chat_id' => $chatId,
                'parse_mode' => 'HTML',
                'text' => $e->getMessage()
            ]);

        } catch (MailboxFetchingException $e) {
            Telegram::sendMessage([
                'chat_id' => $chatId,
                'parse_mode' => 'HTML',
                'text' => $e->getMessage()
            ]);
        }
    }

}
