<?php

namespace Imdgr886\Order\Admin\Controllers;

use App\Admin\Actions\Order\ManualDiscount;
use App\Admin\Actions\Order\PayAfterIssued;
use App\Admin\Extensions\OrderExporter;
use App\Admin\Models\PermissionConstants;
use App\Models\OrderOperationLogs;
use App\Models\Plan;
use App\Models\PlansCombos;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Table;
use \Imdgr886\Order\Models\Order;
use Imdgr886\Order\Models\OrderRefund;
use Imdgr886\Order\Models\OrderTransaction;

class OrderController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '订单管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Order());

        $this->filterGrid($grid);

        $grid->model()->orderBy('created_at', 'desc');
        $grid->model()->with('items');




        $grid->column('id', '订单号')->width('150')->expand(function (Order $model) {
            $items = $model->items;
            $data = [];
            foreach ($items as $item) {
                // if ($item->product instanceof Plan) {
                //     $data[] = [
                //         Plan::PLAN_TYPE_ENUM[$item->product->plan_type],
                //         in_array($item->product->plan_type, [Plan::PLAN_TYPE_VAT, Plan::PLAN_TYPE_IOSS]) ? $item->product->country->name . ':' . $item->product->name : $item->product->name,
                //         $item->price,
                //         $item->quantity,
                //         $item->total
                //     ];
                // } elseif ($item->product instanceof PlansCombos) {
                //     $data[] = [
                //         '套餐',
                //         $item->name,
                //         $item->price,
                //         $item->quantity,
                //         $item->total
                //     ];
                // }
                $data[] = [
                    $item->name,
                    $item->price,
                    $item->qty,
                    $item->total
                ];
            }
            return new Table(['名称', '价格', '数量', '合计'], $data);
        });
        $grid->column('user.name', '用户')->display(function ($value) {
            // $url = route('admin.users.index', ['mobile' => $this->user->mobile ?? '']);
            return "<a href='/users?mobile={$this->user->mobile}' target='_blank'>$value</a>";
        });
        $grid->column('customer_service', '客服')->display(function () {
            return $this->user->customerService->name ?? '';
        });
        $grid->column('seller', '销售')->display(function () {
            return $this->user->seller->name ?? '';
        });
        $grid->column('user.source_name', '用户来源');
        $grid->column('order_amount', '订单金额')->sortable()->display(function () {
            return $this->order_amount ;
            // ($this->discount_amount > 0 ? "<br/><span class='text-muted'>({$this->items_total} - {$this->discount_amount})</span>" : '');
        })->totalRow(function ($amount) {
            return "<span class='text-danger text-bold'><i class='fa fa-yen'></i> {$amount} 元</span>";
        });
        $grid->column('paid_amount', '支付金额');
        $grid->column('transaction.payment_gateway', '支付平台')->display(function ($value) {
            return $value ? OrderTransaction::GATEWAY_ENUM[$value] : '';
        });

        //        $grid->column('items', '商品/服务')->display(function () {
        //            $display = '';
        //            $countryEnum = Country::getCountryCodeEnum();
        //            foreach ($this->items as $item) {
        //                $display .= $countryEnum[$item->product->country_code]. ' : ' . $item->name . ' x ' . $item->quantity . '<br/>';
        //            }
        //            return trim($display, "<br/>");
        //        });

        $grid->column('status', '订单状态')
            ->using(Order::$statusLabels)->dot([
                'pending' => 'default',
                'paid' => 'success',
                'canceled' => 'warning',
                'refunded' => 'danger',
                'partial_refunded' => 'danger'
            ]);
        $grid->column('order_type', '订单类型');
        //$grid->column('transaction_id', '交易流水号');
        $grid->column('created_at', ' 下单时间');

        // $grid->disableActions();

        $grid->actions(function (Grid\Displayers\Actions $actions) {

            // if (checkSlugAuth(PermissionConstants::DELETE_ORDER)) {
            //     $actions->disableDelete(false);
            // } else {
            //     $actions->disableDelete();
            // }
            // $actions->disableEdit();
            // // $actions->disableView();
            // if ($actions->row->status == Order::PENDING) {
            //     $actions->add(new PayAfterIssued());
            //     if (checkSlugAuth(PermissionConstants::ORDER_MANUAL_DISCOUNT)) {
            //         $actions->add(new ManualDiscount());
            //     }
            // }
            if ($actions->row->transaction && in_array($actions->row->transaction->payment_gateway, [OrderTransaction::GATEWAY_ALI, OrderTransaction::GATEWAY_WECHAT])) {
                $actions->add(new \Imdgr886\Order\Admin\ActionWidgets\ApplyRefund());
            }
        });

        $grid->disableCreateButton();
        $grid->disableExport(false);
        return $grid;
    }

    public function show($id, Content $content)
    {
        $order = Order::query()->scopes(['roleData'])->findOrFail($id);
        return $content
            ->title($this->title())
            ->description($this->description['show'] ?? trans('admin.show'))
            ->body(view('admin.order.show', [
                'detail' => $this->detail($order),
                'transactions' => $this->transactionsGrid($order)->render(),
                'refunds' => $this->refundGrid($order)->render(),
                'logs' => $this->operationLogs($order)->render(),
            ]));
        //->body($this->detail($id));
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($order)
    {
        $show = new Show($order);

        $show->field('id', '订单号');
        $show->field('user.name', '用户');
        $show->field('order_amount', '订单金额');
        //        $show->field('subject', __('Subject'));
        //        $show->field('description', __('Description'));
        $show->field('items_total', '总金额');
        $show->field('discount_amount', '优惠金额');

        $show->field('paid_amount', '已支付金额');
        $show->field('transaction_id', '交易流水号');
        $show->field('invoice_no', '发票号');
        $show->field('statusLabel', '订单状态');

        // $show->field('refund_amount', '退款金额');
        $show->field('ip', '用户 IP');
        $show->field('source', '订单来源');
        $show->field('created_at', '下单时间');
        $show->field('paid_at', '支付时间');

        $show->panel()
            ->tools(function ($tools) {
                $tools->disableEdit();
                // $tools->disableList();
                $tools->disableDelete();
            });
        return $show;
    }

    protected function filterGrid(Grid $grid)
    {
        $grid->filter(function (Grid\Filter $filter) {
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            $filter->column(4, function ($filter) {
                $filter->like('id', ' 订单号');
            });
            $filter->column(4, function ($filter) {
                $filter->like('user.mobile', '手机号');
            });
            $filter->column(4, function ($filter) {
                $filter->like('user.name', '用户名');
            });
            $filter->column(4, function ($filter) {
                $filter->between('order_amount', '订单金额');
            });
            $filter->column(4, function ($filter) {
                $filter->between('created_at', '下单时间')->date();
            });
        });
        $grid->selector(function (Grid\Tools\Selector $selector) {
            $selector->selectOne('status', '状态', [
                '' => '全部',
                'pending' => '待付款',
                'paid' => '已支付',
                'wait_approve' => '付款待审核',
                'canceled' => '已取消',
                'refunded' => '全额退款',
                'partial_refund' => '部分退款',
            ], function ($query, $value) {
                if ($value)
                    $query->where('status', $value);
            });
        });
    }

    protected function operationLogs(Order $order)
    {
        $grid = new Grid(new OrderOperationLogs());
        $grid->model()->orderBy('created_at', 'desc')->where('order_id', $order->id);

        $grid->column('created_at', '时间');
        $grid->column('operation_text', '操作');
        $grid->column('operator', '操作人');
        $grid->column('remark', '备注');
        $grid->column('pay_amount', '应付金额')->display(function () {
            return $this->amount_info['after']['order_amount'] ?? $this->order->order_amount;
        });

        $grid->disableRowSelector();
        $grid->disableCreateButton();
        $grid->disableBatchActions();
        $grid->disableFilter();
        $grid->disableColumnSelector();
        $grid->disablePagination();
        $grid->disableActions();
        $grid->setTitle('订单操作日志');
        return $grid;
    }

    protected function transactionsGrid(Order $order)
    {
        $grid = new Grid(new OrderTransaction());
        $grid->model()->where('order_id', $order->id);
        $grid->column('id', '交易号');
        $grid->column('order_amount', '交易金额');
        $grid->column('payment_gateway', '支付渠道');
        $grid->column('created_at', '交易发起时间');
        $grid->column('paid_at', '已支付')->bool();
        $grid->column('paid_time', '支付时间')->display(function () {
            return $this->paid_at;
        });
        //$grid->column('notifies', '详细支付结果');
        //$grid->column('paid_at')
        $grid->disableRowSelector();
        $grid->disableCreateButton();
        $grid->disableBatchActions();
        $grid->disableFilter();
        $grid->disableColumnSelector();
        $grid->disablePagination();
        $grid->disableActions();
        $grid->setTitle('订单交易记录');
        return $grid;
    }

    protected function refundGrid(Order $order)
    {
        $grid = new Grid(new OrderRefund());
        $grid->model()->where('order_id', $order->id)->with('approvedBy');
        $grid->column('id', '退款流水号');
        $grid->column('refund_amount', ' 退款金额');
        $grid->column('payment_gateway', '支付渠道');
        $grid->column('created_at', '退款申请时间');
        $grid->column('status', '退款状态')->using(OrderRefund::$statusLabels);
        $grid->column('approvedBy.name', '审核');
        $grid->column('approved_at', '审核时间');

        //$grid->column('notifies', '详细支付结果');
        //$grid->column('paid_at')
        $grid->disableRowSelector();
        $grid->disableCreateButton();
        $grid->disableBatchActions();
        $grid->disableFilter();
        $grid->disableColumnSelector();
        $grid->disablePagination();
        $grid->disableActions();
        $grid->setTitle('订单退款记录');
        return $grid;
    }
}
