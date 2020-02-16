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
    public function webhook(){
        $telegram=Telegram::getWebhookUpdates()['message'];
        if(!TelegramUser::find($telegram['from']['id'])){
            TelegramUser::create(json_decode($telegram['from'],true));
        }

        if (preg_match("/^.+@.+\..+$/", $telegram["text"])) {
            $domain = substr(strrchr($telegram["text"], "@"), 1);
            $res = getmxrr($domain, $mx_records, $mx_weight);
            if ( !$res || !count($mx_records) || (count($mx_records)==1 && (!$mx_records[0] || $mx_records[0] == "0.0.0.0"))) {
                Telegram::sendMessage([
                    'chat_id' => $telegram['from']['id'],
                    'parse_mode' => 'HTML',
                    'text' => 'Wrong email'
                ]);
            } else {
                $email=DB::table('mailboxes')->where('email',$telegram["text"])->first();
                if($email->id){
                    $client=$this->getClient($email->host, $email->port, $email->email, $email->password,$telegram['from']['id']);
                    $connect=$this->getConnect($client,$telegram['from']['id']);
                    $this->sendFolderInbox($connect,$telegram['from']['id']);
                }else{
                    Telegram::sendMessage([
                        'chat_id' => $telegram['from']['id'],
                        'parse_mode' => 'HTML',
                        'text' => 'not email DB'
                    ]);
                }
            }
        }
        Telegram::commandsHandler(true);
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
                    $text.=sprintf('%s: %s'.PHP_EOL,"Количество присланных не прочитанных сообщений",$oFolder->search()->unseen()->leaveUnread()->setFetchBody(false)->setFetchAttachment(false)->since(now()->subDays(5))->get()->count());
                    Telegram::sendMessage([
                        'chat_id' => $chatId,
                        'parse_mode' => 'HTML',
                        'text' =>$text
                    ]);
                    foreach ($aMessageUnseen5Days as $oMessage) {
                        $text=sprintf('%s: %s'.PHP_EOL,"Ваш идентификатор пользователя",$oMessage->getUid());
                        $text.=sprintf('%s: %s'.PHP_EOL,"Тема ",$oMessage->getSubject());
                        $text.=sprintf('%s: %s'.PHP_EOL,"Отправитель",$oMessage->getSender()[0]->personal);
                        $text.=sprintf('%s: %s'.PHP_EOL,"Дата отправления",$oMessage->getDate('d-M-y'));
                        $text.=sprintf('%s: %s'.PHP_EOL,"Почта отправителя",$oMessage->getFrom()[0]->mail );
                        $text.=sprintf('%s: %s'.PHP_EOL,"Количество вложенний в почте",$oMessage->getAttachments()->count() > 0 ? 'yes' : 'no' );
                        $text.=sprintf('%s: %s'.PHP_EOL,"Само письмо",$oMessage->getTextBody(true) );
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
