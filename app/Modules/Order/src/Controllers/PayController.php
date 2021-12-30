<?php
/**
 * Created by Cartman Chen <thrall.chen@gmail.com>.
 * Author: 陈章--大官人
 * Github: https://github.com/imdgr
 */

namespace Imdgr886\Order\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Routing\UrlGenerator;
use Imdgr886\Order\Models\Order;
use Imdgr886\Order\Models\OrderTransaction;
use Yansongda\LaravelPay\Facades\Pay;

class PayController extends Controller
{

    public function alipay(Order $order)
    {
        if ($order->paid_at) {
            return response(200, "订单不能重复支付");
        }
        $method = request()->get('method', 'web');
        $params = $this->paymentParams($order, 'alipay', $method);
        // $params['notify_url'] = route('alipay-notify');
        
        switch ($method) {
            case 'scan':
                return response()->json(Pay::alipay(config('pay.alipay'))->scan($params));
            default:
                return response()->json(Pay::alipay(config('pay.alipay'))->$method($params)->send());
        }

    }

    public function wechat(Order $order, $method='web')
    {
        if ($order->is_paid) {
            return response(200, "订单不能重复支付");
        }
        $params = $this->paymentParams($order, 'wechat', $method);
        $params['notify_url'] = route('wechat-notify');
        return Pay::wechat(config('pay.wechat'))->scan($params);
    }

    protected function paymentParams(Order $order, $gateway, $method)
    {
        $transaction = new OrderTransaction();
        $transaction->id = app('snowflake')->id();
        $transaction->order_id = $order->id;
        $transaction->payment_gateway = $gateway;
        $transaction->payment_method = $method;
        $transaction->order_amount = $order->order_amount;
        $transaction->ip = request()->getClientIp();
        $transaction->save();

        $amount =  $transaction->order_amount;

        if ($gateway == 'alipay') {
            return [
                'out_trade_no' => $transaction->id,
                'total_amount' => $amount,
                'subject'      => "支付订单{$order->id}",
            ];
        } else if ($gateway == 'wechat') {
            return [
                'out_trade_no' => $transaction->id,
                'total_fee' => $amount * 100,
                'body' => "支付订单{$order->id}",
            ];
        }

        
    }
}
