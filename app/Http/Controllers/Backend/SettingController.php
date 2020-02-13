<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Setting;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Telegram\Bot\Laravel\Facades\Telegram;

/**
 * Class SettingController
 * @package App\Http\Controllers\Backend
 */
class SettingController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(){
        $url=Setting::getSettings();

        return view('backend.setting',$url);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function  store(Request $request){
        Setting::where('key','!=',null)->delete();
        foreach ($request->except('_token') as $key =>$value){
                $setting =new Setting;
                $setting->key=$key;
                $setting->value=$request->$key;
                $setting->save();


        }
        return redirect()->route('admin.setting.index');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function setwebhook(Request $request){

        $result =$this->sendTelegramData('setwebhook',[
           'query'=>['url'=> $request->url.'/'.Telegram::getAccessToken()]
        ]);
        return redirect()->route('admin.setting.index')->with('status',$result );

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function getwebhookinfo(Request $request){
        $result =$this->sendTelegramData('getWebhookInfo');
        return redirect()->route('admin.setting.index')->with('status',$result );
    }


    /**
     * @param string $route
     * @param array $params
     * @param string $method
     * @return string
     */
    public function sendTelegramData($route='', $params=[], $method='POST'){

        $client = new Client(['base_uri'=>'https://api.telegram.org/bot'.Telegram::getAccessToken().'/']);
        $result=$client->request($method,$route,$params);
        return (string)$result->getBody();
    }


}
