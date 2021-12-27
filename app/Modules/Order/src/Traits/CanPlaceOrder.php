<?php

namespace Imdgr886\Order\Traits;

use Imdgr886\Order\Models\Order;
use Imdgr886\Order\Models\OrderItem;
use Imdgr886\Order\OrderItemInterface;

class CanPlaceOrder 
{
    public function placeOrder($items)
    {
        $order = new Order();
        if (items[0] instanceof OrderItemInterface) {
            $items = [$items];
        }

        foreach ($items as $item) {
            list($product, $qty, $extra) = $item;
            $
        }
    }
}