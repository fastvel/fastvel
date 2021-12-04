<?php
/**
 * Created by Cartman Chen <thrall.chen@gmail.com>.
 * Author: 陈章--大官人
 * Github: https://github.com/imdgr
 */

namespace Imdgr886\Wechat\Http\Controllers;

use EasyWeChat\Kernel\Messages\Message;
use Illuminate\Routing\Controller;
use Imdgr886\Wechat\Http\MessageHandlers\EventHandler;
use Imdgr886\Wechat\Http\MessageHandlers\TextHandler;

class WechatController extends Controller
{
    public function __construct()
    {

    }

    public function serve()
    {
        $app = app('wechat.official_account');
        $app->server->push(TextHandler::class, Message::TEXT);
        $app->server->push(EventHandler::class, Message::EVENT);
//        $app->server->push(function($message){
//            return "欢迎关注！";
//        });

        return $app->server->serve();
    }
}
