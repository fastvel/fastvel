<?php
/**
 * Created by Cartman Chen <thrall.chen@gmail.com>.
 * Author: 陈章--大官人
 * Github: https://github.com/imdgr
 */

namespace Imdgr886\Order\Controllers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Imdgr886\Order\Events\OrderPaidEvent;
use Imdgr886\Order\Models\Order;
use Imdgr886\Order\Models\OrderRefund;
use Imdgr886\Order\Models\OrderTransaction;
use Imdgr886\Order\Models\OrderTransactionNotify;
use Yansongda\LaravelPay\Facades\Pay;

class WebhookController extends Controller
{

    public function alipay()
    {
        $alipay = Pay::alipay(config('pay.alipay'));
        $data = $alipay->verify();
        // 交易号
        $tradeId = $data->get('out_trade_no');
        // 支付平台流水号
        $tradeNo = $data->get('trade_no');
        // 支付金额
        $paidAmount = $data->get('total_amount');
        // 支付时间
        $paidAt = Carbon::parse($data->get('gmt_payment'));

        $status = [
            'TRADE_SUCCESS' => '交易支付成功',
            'TRADE_FINISHED' => '交易结束，不可退款',
            'TRADE_CLOSED' => '未付款交易超时关闭，或支付完成后全额退款'
        ];
        $tradeStatus = $data->get('trade_status');
        $message = collect($status)->get($tradeStatus, '');

        $this->recordNotify($tradeId, $tradeStatus, $message, $data);

        if ($data->get('refund_fee')) {
            //退款通知
            $this->handleAlipayRefund($data);
        } else if ($tradeStatus == 'TRADE_SUCCESS') {

            $this->handlePaid($tradeId, $paidAmount, $paidAt, $tradeNo);
        }
        return $alipay->success();
    }

    public function wechat()
    {
        $wechat = Pay::wechat(config('pay.wechat'));
        $data = $wechat->verify();
        // 交易号
        $tradeId = $data->get('out_trade_no');
        // 支付平台流水号
        $tradeNo = $data->get('transaction_id');
        // 支付金额
        $paidAmount = $data->get('total_fee') / 100;
        // 支付时间
        $paidAt = Carbon::parse($data->get('gmt_payment'));

        $this->recordNotify($tradeId, $data->get('result_code'), $data->get('err_code_des'), $data);

        if ($data->get('out_refund_no')) {
            // 退款
            $this->handleWechatRefund($data);
        } else if ($data->get('result_code') == 'SUCCESS') {
            $this->handlePaid($tradeId, $paidAmount, $paidAt, $tradeNo);
        }
        return $wechat->success()->send();
    }

    protected function recordNotify($transId, $event, $result, $data)
    {
        // 异步保存
        dispatch(function () use ($transId, $event, $result, $data) {
            $notify = new OrderTransactionNotify([
                'transaction_id' => $transId,
                'event' => $event,
                'result' => $result,
                'data' => $data
            ]);
            $notify->save();
        });
    }

    protected function handlePaid($transId, $paidAmount, $paidAt, $tradeNo)
    {
        // 不论结果，先记录
        /** @var OrderTransaction */
        $trans = OrderTransaction::findOrFail($transId);
        DB::beginTransaction();
        try {
            /** @var Order */
            //$order = $trans->order;
            $order = Order::query()->where('id', $trans->order_id)->lockForUpdate()->first();

            $trans->paid_amount = $paidAmount;
            $trans->paid_at = $paidAt;
            $trans->trade_no = $tradeNo;
            $trans->save();

            if ($order->paid_at) {
                if ($order->transaction_id != $transId) {
                    // 重复支付
                    $order->addHistory('重复支付');
                }
                throw new \Exception('重复支付');
            }

            $order->transaction_id = $transId;
            $order->paid_amount += $paidAmount;
            $order->paid_at = $paidAt;
            $order->status = 'paid';
            $order->save();
            
            $order->addHistory('支付成功');
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
        event(new OrderPaidEvent($order));

    }

    public function handleAlipayRefund($data)
    {
        $refundedAt = Carbon::parse($data->get('gmt_refund'));
        $refundId = $data->get('out_biz_no');
        $refund = OrderRefund::find($refundId);
        //if ($refund && $refund->status == 'refunding') {
            $refund->status = 'refunded';
            $refund->refunded_at = $refundedAt;
            $refund->save();
        //}
    }

    public function handleWechatRefund($data)
    {
        $refundedAt = Carbon::parse($data->get('success_time', date('Y-m-d H:i:s')));
        $refundId = $data->get('out_refund_no');
        $refund = OrderRefund::find($refundId);

        //if ($refund && $refund->status == 'refunding') {
            $refund->status = $data->get('refund_status') == 'SUCCESS' ? 'refunded': 'failed';
            $refund->refunded_at = $refundedAt;
            $refund->save();
        //}
    }
}
