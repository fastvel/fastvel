<?php

namespace App\Listeners;

use App\Models\DevicePlan;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
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

        foreach ($event->order->items as $item) {
            [
                'team_id' => $event->order->owner_id,
                'expires_at' => Carbon::now()->addMonths(),
                'provider' => '',
                'instance_mode'
            ]
        }
        
    }

    protected function renewService(DevicePlan $pan)
    {
        
    }

    protected function createService($service)
    {
        
    }
}
