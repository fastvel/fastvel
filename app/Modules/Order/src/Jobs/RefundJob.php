<?php

namespace Imdgr886\Order\Jobs;

use App\Models\AgentService;
use App\Models\IossService;
use App\Models\Plan;
use App\Models\PlansCombos;
use App\Models\Service;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Imdgr886\Order\Models\OrderItem;
use Imdgr886\Order\Models\OrderRefund;
use Yansongda\LaravelPay\Facades\Pay;

/**
 * Class RefundJob
 * 执行退款请求
 * @package App\Jobs
 */
class RefundJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $refund;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(OrderRefund $refund)
    {
        $this->refund = $refund;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->refund->canRefund()) {
            $this->executeRefund($this->refund);
            // $this->isCheck();
        }
    }

    protected function executeRefund($refund)
    {
        switch ($refund->transaction->payment_gateway) {
            case 'alipay':
            case 'aliwap':
                $client = Pay::alipay('config.pay.alipay');
                $params = [
                    'out_trade_no' => $refund->transaction_id,
                    'refund_amount' => $refund->refund_amount,
                    'out_request_no' => $refund->id,
                ];
                break;
            case 'wechat':
                $client = Pay::wechat('config.pay.wechat');
                $params = [
                    'out_trade_no' => $refund->transaction_id,
                    'out_refund_no' => $refund->id,
                    'total_fee' => $refund->transaction->paid_amount * 100,
                    'refund_fee' => $refund->refund_amount * 100,
                ];
                break;
            default:
                return;
        }
        try {
            $client->refund($params);

            foreach ($refund->stop_order_items as $itemId) {
                $orderItem = OrderItem::query()
                    ->where('order_id', $refund->order_id)
                    ->where('item_id', $itemId)
                    ->first();
                if ($orderItem->product instanceof Plan) {
                    $plan = $orderItem->product;
                    $service = $plan->getPlanService();
                    if($service instanceof IossService){
                        $iossServices = $service->where('order_id', $refund->order_id)
                            ->where('plan_id', $orderItem->product->id)
                            ->get();
                        foreach ($iossServices as $ioss){
                            if($ioss->vat){
                                $ioss->vat->delete();
                            }
                            $ioss->delete();
                        }
                    }else{
                        $service->where('order_id', $refund->order_id)
                            ->where('plan_id', $orderItem->product->id)
                            ->delete();
                    }

                } elseif ($orderItem->product instanceof PlansCombos) {
                    $plans = $orderItem->product->plans;
                    foreach ($plans as $plan) {
                        $service = $plan->getPlanService();
                        if($service instanceof IossService){
                            $iossServices = $service->where('order_id', $refund->order_id)
                                ->where('plan_id', $plan->id)
                                ->get();
                            foreach ($iossServices as $ioss){
                                if($ioss->vat){
                                    $ioss->vat->delete();
                                }
                                $ioss->delete();
                            }
                        }else{
                            $service->where('order_id', $refund->order_id)
                                ->where('plan_id', $plan->id)
                                ->delete();
                        }
                    }
                }
            }
            $refund->status = OrderRefund::REFUNDED;
            $refund->save();
        } catch (\Exception $e) {
            $refund->status = OrderRefund::FAILED;
            $refund->comment = $e->getMessage();
            $refund->save();
            Log::error('退款接口调用失败:' . $e->getMessage());
        }
    }

    /**
     * 判断是否检查退款结果
     */
    protected function isCheck()
    {
        $trans = $this->refund->transaction;
        if ($trans->payment_gateway == 'alipay' && $trans->payment_method != 'app') {
            // 支付宝全额退款时，可能没有通知
            if ($this->refund->order->canRefundAmount() <= 0) {
                // 延迟一定时间后检查退款状态
                dispatch(new RefundQueryJob($this->refund))->delay(now()->addMinutes(1));
            }
        }
    }


}
