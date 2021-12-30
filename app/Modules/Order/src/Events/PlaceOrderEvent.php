<?php

namespace Imdgr886\Order\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Imdgr886\Order\Models\Order;

class PlaceOrderEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    
    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }
}