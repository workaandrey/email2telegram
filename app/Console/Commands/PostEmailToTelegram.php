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
    protected $description = 'Send user email to telegram';

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
            $client = $this->getClient($email->host, $email->port, $email->encryption, true, $email->email, $email->password, 'imap', config('telegram.bots.common.channel'));
            $connect = $this->getConnect($client, config('telegram.bots.common.channel'));
            $this->sendFolderInbox($connect, config('telegram.bots.common.channel'));
        }
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
    public function getClient($host, $port, $encryption, $validate_cert, $username, $password, $protocol, $chatId){
        try {
            $oClient = new Client([
                'host' => $host,
                'port' => $port,
                'encryption' => $encryption,
                'validate_cert' => $validate_cert,
                'username' => $username,
                'password' => $password,
                'protocol' => $protocol
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
                    $text.=sprintf('%s: %s'.PHP_EOL,"Количество присланных не прочитанных сообщений",$oFolder->search()->unseen()->leaveUnread()->setFetchBody(false)->setFetchAttachment(false)->since(now()->subDays(10))->get()->count());
                    Telegram::sendMessage([
                        'chat_id' => $chatId,
                        'parse_mode' => 'HTML',
                        'text' =>$text
                    ]);
                    foreach ($aMessageUnseen5Days as $oMessage) {
                        $post=sprintf('%s: %s'.PHP_EOL,"Ваш идентификатор пользователя",$oMessage->getUid());
                        $post.=sprintf('%s: %s'.PHP_EOL,"Тема ",$oMessage->getSubject());
                    //    $post.=sprintf('%s: %s'.PHP_EOL,"Отправитель",$oMessage->getSender()[0]->personal);
                        $post.=sprintf('%s: %s'.PHP_EOL,"Дата отправления",$oMessage->getDate('d-M-y'));
                      //  $post.=sprintf('%s: %s'.PHP_EOL,"Почта отправителя",$oMessage->getFrom()[0]->mail );
                     //   $post.=sprintf('%s: %s'.PHP_EOL,"Количество вложенний в почте",$oMessage->getAttachments()->count() > 0 ? 'yes' : 'no' );
                    //    $post.=sprintf('%s: %s'.PHP_EOL,"Само письмо",$oMessage->getTextBody(true) );
                        Telegram::sendMessage([
                            'chat_id' => $chatId,
                            'parse_mode' => 'HTML',
                            'text' =>$post
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
                'text' => $e->getMessage().'lkll'
            ]);
        }
    }

}
