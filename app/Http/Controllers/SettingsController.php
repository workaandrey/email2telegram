<?php


namespace App\Http\Controllers;


use App\Http\Requests\SettingsRequest;
use App\Services\TelegramService;

class SettingsController extends Controller
{
    public function index()
    {
        return view('settings.index');
    }

    public function save(SettingsRequest $request, TelegramService $telegramService)
    {
        try {
            $token = $request->input('telegram_token');
            $attributes = ['telegram_token' => $token];
            if($token != auth()->user()->telegram_token || empty(auth()->user()->telegram_chat_id)) {
                config(['telegram.bots.common.token' => $token]);
                $attributes['telegram_chat_id'] = $telegramService->getBotChatId($token);
            }
            auth()->user()->update($attributes);
        } catch (\Exception $e) {
            flash($e->getMessage())->error();
        }

        return back();
    }
}
