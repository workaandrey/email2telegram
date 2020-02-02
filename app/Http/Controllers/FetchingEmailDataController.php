<?php

namespace App\Http\Controllers;


use Webklex\IMAP\Client;
use Webklex\IMAP\Exceptions\ConnectionFailedException;
use Webklex\IMAP\Exceptions\MailboxFetchingException;
use Webklex\IMAP\Exceptions\MaskNotFoundException;

/**
 * Class FetchingEmailDataController
 * @package App\Http\Controllers
 */
class FetchingEmailDataController extends Controller
{

    //https://hotexamples.com/examples/-/-/imap_fetchbody/php-imap_fetchbody-function-examples.html
    //set_time_limit
    //imap.mail.ru:993/imap/ssl}
    // 'imap.yandex.ru:993/ssl/novalidate-cert/readonly}'
    //'imap.gmail.com'

    /**
     * @throws ConnectionFailedException
     * @throws MailboxFetchingException
     * @throws MaskNotFoundException
     */
    public function gettingEmails()
    {

            $oClient = new Client([
                'host' => 'imap.mail.ru:993/imap/ssl}',
                'port' => 993,
                'encryption' => 'ssl',
                'validate_cert' => true,
                'username' => 'email',
                'password' => 'password',
                'protocol' => 'imap'
            ]);


            $oClient->connect();



            $aFolder = $oClient->getFolders();

              foreach($aFolder as $oFolder) {
                  echo $oFolder->name;
                  if ($oFolder->name == 'INBOX') {
                      echo $nameFolders = $oFolder->name;
                      $aMessageUnseen5Days = $oFolder->query()->since(now()->subDays(1-0))->get();
                      echo $countaMessageUnseen5Days = $oFolder->search()->unseen()->leaveUnread()->setFetchBody(false)->setFetchAttachment(false)->since(now()->subDays(100))->get()->count();
                      foreach ($aMessageUnseen5Days as $oMessage) {
                          echo $oMessage->getUid() . '<br />';
                          echo $oMessage->getSubject() . '<br />';
                          echo $oMessage->getSender()[0]->personal.'<br />';
                          echo $oMessage->getDate('d-M-y') . '<br />';
                          echo $oMessage->getFrom()[0]->mail . '<br />';
                          echo 'Attachments: ' . $oMessage->getAttachments()->count() > 0 ? 'yes' : 'no' . '<br />';
                          echo $oMessage->getHTMLBody(true) . '<br />';
                          echo '<br />' . '<br />' . '<br />';
                      }
                  }

              }


    }


}
