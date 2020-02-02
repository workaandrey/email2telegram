<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\Laravel\Facades\Telegram;
//use Illuminate\Support\Facades\App;


/**
 * Class TelegramBotController
 * @package App\Http\Controllers
 */
class TelegramBotController extends Controller
{
    /**
     *
     */
    public function updatedActivity()
    {
    $activity = Telegram::getUpdates();
    dd($activity);

    }

    /**
     * @return Factory|View
     */
    public function sendMessage()
    {
        return view('message');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function storeMessage(Request $request)
    {

        $request->validate([
            'email' => 'required|email',
            'message' => 'required','string'
        ]);

        $text = "A new contact us query\n"
            . "<b>Email Address: </b>\n"
            . "$request->email\n"
            . "<b>Message: </b>\n"
            . $request->message;

        Telegram::sendMessage([
            'chat_id' => config('telegram.bots.common.channel'),
            'parse_mode' => 'HTML',
            'text' => $text
        ]);

        return redirect()->back();
    }

    /**
     * @return Factory|View
     */
    public function sendPhoto()
    {
        return view('photo');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     * @throws TelegramSDKException
     */
    public function storePhoto(Request $request)
    {
        $request->validate([
            'file' => 'file|mimes:jpeg,png,gif'
        ]);

        $photo = $request->file('file');
        $file= new InputFile($photo);
        Telegram::sendPhoto([
            'chat_id' => config('telegram.bots.common.channel'),
            'photo' => $file->open()
        ]);

        return redirect()->back();
    }
}
