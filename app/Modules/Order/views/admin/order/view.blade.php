<div class="box">
    <div class="box-body">
        <table class="table table-bordered" style="max-width: 1000px;">
            <tr>
                <th>订单号</th>
                <td>{{$order->id}}</td>
                <th>订单金额</th>
                <td>{{$order->order_amount}}
                    @if ($order->discount_amount > 0)
                        ({{$order->items_total}} - {{$order->discount_amount}})
                    @endif
                </td>

                <th>订单状态</th>
                <td>

                        <span class="text-red">{{ $order->status_label }}（已付￥{{$order->paid_amount}}）</span>
                </td>
            </tr>
            <tr>

                <th>支付流水号</th>
                <td>{{$order-> transaction_no}}</td>
                <th>支付平台</th>
                <td>
                    {{$order->transaction ? $order->transaction->payment_gateway : ''}}
                </td>
                <th>支付时间</th>
                <td>
                    {{$order->paid_at}}
                </td>
            </tr>
            <tr>
                <th>已退金额</th>
                <td>
                    {{$order->refund_amount}}
                </td>
                <th>退款次数</th>
                <td>
                    {{$order->refunds()->count()}}
                </td>
                <th>发票</th>
                <td>
                    {{ $order->invoice_no }}
                </td>
            </tr>
            <tr>
                <th>用户名</th>
                <td>{{$order->user->name}}</td>
                <th>手机号</th>
                <td>{{$order->user->mobile}}</td>
                <th>邮箱</th>
                <td>{{$order->user->email}}</td>
            </tr>


        </table>
    </div>
</div>

