<?php
/**
 * Created by Cartman Chen <thrall.chen@gmail.com>.
 * Author: 陈章--大官人
 * Github: https://github.com/imdgr
 */

namespace Imdgr886\Order;

use Encore\Admin\Extension;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Imdgr886\Order\Admin\Controllers\OrderController;

class OrderAdmin extends Extension
{
    public static function boot()
    {
        parent::boot();
        static::loadRoutes();
    }

    public static function loadRoutes()
    {
        parent::routes(function (Router $router) {
            $router->resource('orders', OrderController::class);
            $router->get('refund/{order}', '\Imdgr886\Order\Admin\Controllers\RefundController@create');
            $router->post('refund/{order}', '\Imdgr886\Order\Admin\Controllers\RefundController@store');
            $router->get('refunds', '\Imdgr886\Order\Admin\Controllers\RefundController@index');
        });
    }
}
