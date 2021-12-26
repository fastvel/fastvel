<?php

namespace App\Http\Controllers;

use App\Models\DevicePlan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Imdgr886\Order\Events\OrderPaidEvent;
use Imdgr886\Order\Models\Order;
use Imdgr886\Order\Models\OrderItem;

class OrderController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'plan_id' => ['required', 'exists:device_plans,id'],
            'qty' => ['required', 'integer', 'min:10', 'max:99'],
            'duration' => ['required', 'integer', 'in:1,3,6,12']
        ], [],[
            'qty' => '购买数量',
            'plan_id' => '套餐',
            'duration' => '服务时长'
        ]);


        $order = Order::create([
            'user_id' => auth('api')->user()->id,
            'subject' => '购买专线猫服务',
            'items_total' => 100,
            'discount_amount' => 10,
            'order_amount' => 90,
            'ip' => $request->getClientIp(),
        ]);
        DB::beginTransaction();
        try {
            $order->save();
//            if (isset($coupon)) {
//                $userCoupon = UserCoupon::query()->lockForUpdate()->find($coupon->id);
//                /*$coupon->refresh()->lockForUpdate();*/
//                $userCoupon->used_by_order = $order->id;
//                $userCoupon->save();
//            }
            /*$order->items()->saveMany($itemsModel);*/
            $order->items()->createMany([
                [
                    'item_type' => DevicePlan::class,
                    'item_id' => 1,
                    'quantity' => 1,
                    'price' => 68,
                    'total' => 68,
//                    'order_type' => OrderItem::ORDER_TYPE_OTHER,
                    'name' => 'aws'
                ]
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            throw $e;
        }


        if ($order->order_amount <= 0) {
            $order->is_paid = true;
            $order->status = Order::PAID;
            $order->paid_at = Carbon::now();
            $order->save();
            event(new OrderPaidEvent($order));
        } else {
        }
        //event(new OrderOperationLogEvent($order, OrderOperationLogs::ORDER_CREATED_TYPE));

        return response()->json($order);
    }
}
