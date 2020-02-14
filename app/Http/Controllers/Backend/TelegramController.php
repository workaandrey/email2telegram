<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\TelegramUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Telegram\Bot\Laravel\Facades\Telegram;
use Webklex\IMAP\Client;
use Webklex\IMAP\Exceptions\ConnectionFailedException;
use Webklex\IMAP\Exceptions\MailboxFetchingException;
use Webklex\IMAP\Exceptions\MaskNotFoundException;


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
     *
     */
    public function webhook(){
        $this->telegram=Telegram::getWebhookUpdates()['message'];
        if(!TelegramUser::find( $this->telegram['from']['id'])){
            TelegramUser::create(json_decode($this->telegram['from'],true));
        }
        Telegram::commandsHandler(true);

        if (preg_match("/^.+@.+\..+$/", $this->telegram["text"])) {
            $domain = substr(strrchr($this->telegram["text"], "@"), 1);
            $res = getmxrr($domain, $mx_records, $mx_weight);
            if ( !$res || !count($mx_records) || (count($mx_records)==1 && (!$mx_records[0] || $mx_records[0] == "0.0.0.0"))) {
                Telegram::sendMessage([
                    'chat_id' =>  $this->telegram['from']['id'],
                    'parse_mode' => 'HTML',
                    'text' => 'Wrong email'
                ]);
            } else {
             $this->telegramMail();
            }
        }

    }

    /**
     *
     */
    public function telegramMail(){
    $email=DB::table('mailboxes')->where('email',$this->telegram["text"])->first();
        if($email->id){
               $client=$this->getClient($email->host, $email->port,$email->encryption,true, $email->email, $email->password,'imap',$this->telegram['from']['id']);
               $connect=$this->getConnect($client, $this->telegram['from']['id']);
               $this->sendFolderInbox($connect, $this->telegram['from']['id']);


        }else{
               Telegram::sendMessage([
                   'chat_id' =>  $this->telegram['from']['id'],
                   'parse_mode' => 'HTML',
                   'text' => 'no email in the database'
        ]);
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
     * @return mixed
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
                    $post.=sprintf('%s: %s'.PHP_EOL,"Отправитель",$oMessage->getSender()[0]->personal);
                    $post.=sprintf('%s: %s'.PHP_EOL,"Дата отправления",$oMessage->getDate('d-M-y'));
                    $post.=sprintf('%s: %s'.PHP_EOL,"Почта отправителя",$oMessage->getFrom()[0]->mail );
                    $post.=sprintf('%s: %s'.PHP_EOL,"Количество вложенний в почте",$oMessage->getAttachments()->count() > 0 ? 'yes' : 'no' );
                    $post.=sprintf('%s: %s'.PHP_EOL,"Само письмо",$oMessage->getTextBody(true) );
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
            'text' => $e->getMessage()
        ]);
    }
}

}
