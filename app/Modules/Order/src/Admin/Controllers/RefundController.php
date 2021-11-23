<?php
/**
 * Created by Cartman Chen <thrall.chen@gmail.com>.
 * Author: 陈章--大官人
 * Github: https://github.com/imdgr
 */

namespace Imdgr886\Order\Admin\Controllers;

use App\Admin\Models\PermissionConstants;
use App\Models\Plan;
use App\Models\PlansCombos;
use App\Models\Service;
use Encore\Admin\Actions\Action;
use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Routing\Controller;
use Imdgr886\Order\Admin\ActionWidgets\BatchApproveRefund;
use Imdgr886\Order\Admin\ActionWidgets\ApplyRefund;
use Imdgr886\Order\Admin\ActionWidgets\BatchRefuseRefund;
use Imdgr886\Order\Admin\ActionWidgets\CancelRefund;
use Imdgr886\Order\Admin\ActionWidgets\ExecuteRefund;
use Imdgr886\Order\Jobs\RefundJob;
use Imdgr886\Order\Models\Order;
use Imdgr886\Order\Models\OrderRefund;

class RefundController extends Controller
{
    protected $title = '退款管理';

    public function create(Order $order, Content $content)
    {
        $content->title('发起退款');
        $content->row(view('admin.order.view', ['order' => $order]));
        if (!$order->is_paid) {
            return $content->withWarning('警告', '订单未支付，不能发起退款！');
        } else if (!$order->transaction_id) {
            return $content->withWarning('警告', '订单非线上支付，暂不能发起退款！');
        }
        if($order->invoice_id){
            return $content->withWarning('警告', '订单已开发票,谨慎退款!');

        }
        return $content->body($this->form($order));
    }

    public function index(Content $content)
    {
        return $content->title($this->title)
            ->description('列表')
            ->body($this->grid());
    }

    protected function form(Order $order)
    {
        $form = new Form(new OrderRefund());

        $form->model()->order_id = $order->id;
        $form->model()->transaction_id = $order->transaction_id;
        $form->model()->apply_by_type = Administrator::class;
        $form->model()->apply_by_id = Admin::user()->id;

        $maxRefund = $order->canRefundAmount();
        $form->text('refund_amount', '退款金额')->rules("required|numeric|min:0|max:{$maxRefund}")
            ->help('最大可退金额 ' . $maxRefund);
        $form->textarea('reason', '退款原因')->rules('required')->rows(6);
        $form->multipleSelect('stop_order_items', '停掉服务')->options(function () use ($order){
            $enum = [];
            foreach ($order->items as $item){
                if($item->product instanceof Plan){
                    switch ($item->product->plan_type){
                        case Plan::PLAN_TYPE_VAT:
                            $name = 'VAT服务:国家' . $item->product->country_code . ':' . $item->product->name;
                            break;
                        case Plan::PLAN_TYPE_AGENT:
                            $name = '代理服务:' . $item->product->name;
                            break;
                        case Plan::PLAN_TYPE_IOSS:
                            $name = 'IOSS服务:' . $item->product->country_code . ':' . $item->product->name;
                            break;
                    }
                }elseif ($item->product instanceof PlansCombos){
                    $name = '套餐服务:' . $item->product->name;
                }
                $enum[$item->item_id] = sprintf('服务名称:%s 服务单价:%s 服务数量:%s', $name, $item->price, $item->quantity);
            }
            return $enum;
        });
        $form->setAction($order->id);

        $form->tools(function ($tools) {
            $tools->disableList();
        });
        $form->saved(function () {
            return redirect()->to('/admin/orders');
        });
        return $form;
    }

    public function store(Order $order)
    {
        return $this->form($order)->store();
    }


    protected function grid()
    {
        $grid = new Grid(new OrderRefund());
        $grid->model()->with('order', 'transaction', 'applyBy', 'approvedBy')->orderBy('created_at', 'desc');
        $grid->column('id', '退款流水号');
        $grid->column('order.id', '订单号')->link(function () {
            return '/' . config('admin.route.prefix') . '/orders/' . $this->order->id;
        });
        $grid->column('refund_amount', '退款金额');
        $grid->column('reason', '退款原因');
        $grid->column('transaction.payment_gateway', '支付渠道')->using([
            'alipay' => '支付宝',
            'wechat' => '微信',
        ]);
        $grid->column('status', '状态')->using(OrderRefund::$statusLabels)->dot([
            'pending' => 'info',
            'approved' => 'primary',
            'refunding' => 'primary',
            'refunded' => 'success',
            'canceled' => 'default',
            'refused' => 'default',
            'failed' => 'danger',
        ]);
        $grid->column('approvedBy.name', '审核');
        $grid->column('approved_at', '审核时间');
        $grid->column('applyBy.name', '申请人');
        $grid->column('comment', '备注');

        if (checkSlugAuth(PermissionConstants::VERIFY_REFUND, true)) {
            $grid->tools(function (Grid\Tools $tools) {
                $tools->append(new BatchApproveRefund());
                $tools->append(new BatchRefuseRefund());
            });
        }
        $grid->actions(function(Grid\Displayers\Actions $actions) {
            $actions->disableDelete();
            $actions->disableEdit();
            $actions->disableView();

            if ($actions->row->canRefund()) {
                $actions->add(new ExecuteRefund());
            }
            if ($actions->row->canCancel()) {
                $actions->add(new CancelRefund());
            }
        });
        $grid->filter(function (Grid\Filter $filter) {
            $filter->disableIdFilter();
            $filter->column(6, function (Grid\Filter $filter) {
                $filter->like('id', '退款流水号');
                $filter->like('transaction_id', '交易流水号');
            });
            $filter->column(6, function (Grid\Filter $filter) {
                $filter->like('order_id', '订单号');
                $filter->between('refund_amount', '退款金额');
            });
        });
        return $grid;
    }

    public function execute(OrderRefund $refund)
    {
        dispatch(new RefundJob($refund));
    }
}
