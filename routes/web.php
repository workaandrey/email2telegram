<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
use Telegram\Bot\Laravel\Facades\Telegram;
Route::get('/', function () {
    return cache()->remember('test', 600, function() {
        echo "cache";
        return 'Hello world';
    });
    return view('welcome');
});


/*
Route::get('/<token>/webhook', function () {
    $setWebhook= Telegram::setWebhook(['url'=>'https://api.telegram.org/<token>/webhook']);
dd($setWebhook);
    return 'ok';
});*/
Route::get('/', 'TelegramBotController@sendMessage');
Route::post('/send-message', 'TelegramBotController@storeMessage');
Route::get('/send-photo', 'TelegramBotController@sendPhoto');
Route::post('/store-photo', 'TelegramBotController@storePhoto');
Route::get('/updated-activity', 'TelegramBotController@updatedActivity');
;

Route::get('/getting-emails', 'FetchingEmailDataController@gettingEmails');

