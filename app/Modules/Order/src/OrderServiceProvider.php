<?php

/**
 * Created by Cartman Chen <thrall.chen@gmail.com>.
 * Author: 陈章--大官人
 * Github: https://github.com/imdgr
 */

namespace Imdgr886\Order;

use Encore\Admin\Admin;
use Godruoyi\Snowflake\LaravelSequenceResolver;
use Godruoyi\Snowflake\Snowflake;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Imdgr886\Order\Models\OrderRefund;
use Imdgr886\Order\Observers\OrderRefundObserver;

class OrderServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->configureCommands();

        $this->routes();
        $this->laravelAdminLoad();

        // 退款订单观察者
        OrderRefund::observe(OrderRefundObserver::class);
    }

    protected function configureCommands()
    {
        if (!$this->app->runningInConsole()) {
            return;
        }

        $this->commands([
            Console\InstallCommand::class,
        ]);
    }

    protected function laravelAdminLoad()
    {
        if (class_exists(Admin::class)) {
            OrderAdmin::boot();
        }
    }

    protected function routes()
    {
        Route::group(['middleware' => ['web']],  function () {
            Route::get('/order/{order}/alipay', 'Imdgr886\Order\Controllers\PayController@alipay')->name('alipay-pay');
            Route::get('/order/{order}/wechat', 'Imdgr886\Order\Controllers\PayController@wechat')->name('wechat-pay');
        });

        Route::post('/payment/notify/alipay', 'Imdgr886\Order\Controllers\NotifyController@alipay')->name('alipay-notify');
        Route::post('/payment/notify/wechat', 'Imdgr886\Order\Controllers\NotifyController@wechat')->name('wechat-notify');
    }
}
