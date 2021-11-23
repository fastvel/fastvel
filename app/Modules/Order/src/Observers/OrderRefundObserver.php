<?php
/**
 * Created by Cartman Chen <thrall.chen@gmail.com>.
 * Author: 陈章--大官人
 * Github: https://github.com/imdgr
 */

namespace Imdgr886\Order\Observers;

use Illuminate\Support\Facades\Log;
use Imdgr886\Order\Models\Order;
use Imdgr886\Order\Models\OrderRefund;

class OrderRefundObserver
{
    public function created(OrderRefund $refund)
    {
        if ($refund->order->status != Order::APPLY_REFUND) {
            $refund->order->status = Order::APPLY_REFUND;
            $refund->order->save();
        }
    }

    public function updated(OrderRefund $refund)
    {
        $refund->fireOrderStatusUpdate();
    }

    public function deleted(OrderRefund $refund)
    {

    }
}
