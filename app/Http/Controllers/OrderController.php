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
use Imdgr886\Team\Models\Team;

class OrderController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'plan_id' => ['required', 'exists:device_plans,id'],
            'qty' => ['required', 'integer', 'min:1', 'max:99'],
            'duration' => ['required', 'integer', 'in:1,3,6,12']
        ], [],[
            'qty' => '购买数量',
            'plan_id' => '套餐',
            'duration' => '服务时长'
        ]);


        $product = DevicePlan::query()->findOrFail($request->get('plan_id'));
        $order = auth('api')->user()->current_team->placeOrder([$product, $request->qty, ['duration' => $request->duration]]);

        return response()->json($order);
    }

    public function detail(Order $order)
    {
        return $order;
    }
}
