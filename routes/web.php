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
    return view('welcome');
});

Auth::routes();
Route::middleware(['admin'])->prefix('admin')->namespace('Backend')->name('admin.')->group(function () {
    Route::get('/', 'DashboardController@index')->name('index');
    Route::get('/setting', 'SettingController@index')->name('setting.index');
    Route::post('/setting/store', 'SettingController@store')->name('setting.store');
    Route::post('setting/webhook', 'SettingController@setwebhook')->name('setting.setwebhook');
    Route::post('setting/getwebhookinfo', 'SettingController@getwebhookinfo')->name('setting.getwebhookinfo');

});

Route::post(Telegram::getAccessToken(), 'Backend\TelegramController@webhook');

Route::get('/home', 'HomeController@index')->name('home');

Route::group(['middleware' => 'auth'], function () {
    Route::resource('mailbox', 'MailboxController');
});
