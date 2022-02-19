<?php

/**
 * Created by Cartman Chen <thrall.chen@gmail.com>.
 * Author: 陈章--大官人
 * Github: https://github.com/imdgr
 */

namespace Imdgr886\Order;

use Encore\Admin\Admin;
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
        Route::middleware(['api'])->prefix('api')->group(function () {
            Route::post('/pay/order/{order}/alipay', 'Imdgr886\Order\Controllers\PayController@alipay')->name('alipay-pay');
            Route::post('/pay/order/{order}/wechat', 'Imdgr886\Order\Controllers\PayController@wechat')->name('wechat-pay');
        });

        Route::post('/payment/alipay/notify', 'Imdgr886\Order\Controllers\WebhookController@alipay')->name('alipay-notify');
        Route::post('/payment/wechat/notify', 'Imdgr886\Order\Controllers\WebhookController@wechat')->name('wechat-notify');
    }
}
