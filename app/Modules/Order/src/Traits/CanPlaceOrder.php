<?php

namespace Imdgr886\Order\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Imdgr886\Order\Events\BeforePlaceOrderEvent;
use Imdgr886\Order\Events\OrderPaidEvent;
use Imdgr886\Order\Events\PlaceOrderEvent;
use Imdgr886\Order\Models\Order;
use Imdgr886\Order\Models\OrderItem;
use Overtrue\EasySms\Exceptions\Exception;

trait CanPlaceOrder
{
    /**
     * 下单
     * @param ...$items
     * @return Order
     * @throws \Exception
     */
    public function placeOrder(...$items)
    {
        $order = new Order();

        $itemsTotal = 0;
        $orderItems = [];
        foreach ($items as $item) {
            list($product, $qty, $options) = $item;
            $itemsTotal += $product->getProductAmount($qty, $options);
            $orderItem = new OrderItem([
                'qty' => $qty,
                'options' => $options,
                'name' => $product->getName(),
                'price' => $product->getPrice(),
                'total' => $product->getProductAmount($qty, $options),
            ]);
            $orderItem->product()->associate($product);
            $orderItems[] = $orderItem;
        }
        $order->items_total = $itemsTotal;
        $order->discount_amount = 0;
        $order->order_amount = $itemsTotal;
        $order->user_id = $this->id;
        if ($order->order_amount <= 0) {
            $order->paid();
        }
        DB::beginTransaction();
        try {
            // event(new BeforePlaceOrderEvent($order, $orderItems));
            $order->save();
            $order->items()->saveMany($orderItems);
            DB::commit();
            event(new PlaceOrderEvent($order));
            if ($order->paid_at != null) {
                event(new OrderPaidEvent($order));
            }
            return $order;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
