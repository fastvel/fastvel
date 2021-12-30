<?php

namespace Imdgr886\Order\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Imdgr886\Order\Models\Order;

class BeforePlaceOrderEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;
    public $items;

    public function __construct(Order $order, array $orderItems)
    {
        $this->order = $order;
        $this->items = $orderItems;
    }
}
