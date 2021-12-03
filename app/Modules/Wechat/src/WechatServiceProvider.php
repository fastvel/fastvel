<?php
/**
 * Created by Cartman Chen <thrall.chen@gmail.com>.
 * Author: 陈章--大官人
 * Github: https://github.com/imdgr
 */

namespace Imdgr886\Wechat;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Imdgr886\Wechat\Http\Controllers;

class WechatServiceProvider extends ServiceProvider
{
    public function register()
    {
        parent::register();
    }

    public function boot()
    {

        Route::any('/wechat', Controllers\WeChatController::class . '@serve');
    }
}
