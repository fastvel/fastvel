<?php

namespace App\Listeners;

use App\Models\DevicePlan;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Imdgr886\Order\Events\OrderPaidEvent;

class OrderPaidListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(OrderPaidEvent $event)
    {
        // 判单订单是否已经处理过了

        // 分清是续费还是新购
        foreach ($event->order->items as $item) {
            if ($item->product instanceof DevicePlan) {
                $this->renewService($item->product);
            } else {
                $this->createService($item->product);
            }
        }
        

    }

    protected function renewService(DevicePlan $pan)
    {
        
    }

    protected function createService($service)
    {
        
    }
}
