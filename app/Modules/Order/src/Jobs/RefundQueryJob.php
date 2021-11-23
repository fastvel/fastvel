<?php

namespace Imdgr886\Order\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Imdgr886\Order\Models\OrderRefund;
use Yansongda\LaravelPay\Facades\Pay;

/**
 * Class RefundJob
 * 检查退款结果
 * @package App\Jobs
 */
class RefundQueryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $refund;

    /**
     * 最多重试5次
     * @var int
     */
    public $tries = 3;

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
        if ($this->refund->status == OrderRefund::REFUNDED) {
            return;
        }
        if ($this->refund->transaction->payment_gateway == 'alipay') {
            $this->alipayRefundQuery($this->refund);
        } else if ($this->refund->transaction->payment_gateway == 'alipay') {

        }
    }

    protected function alipayRefundQuery($refund)
    {
        $order = [
            'out_trade_no' => $refund->transaction->id,
            'out_request_no' => $refund->id
        ];

        $result = Pay::alipay(config('pay.alipay'))->find($order, 'refund');
        $refundTime = $result->get('gmt_refund_pay');
        if ($refundTime) {
            $refund->status = OrderRefund::REFUNDED;
            $refund->save();
        } else {
            dispatch(new RefundQueryJob($this->refund))->delay(now()->addHours(2));
        }
    }
}
