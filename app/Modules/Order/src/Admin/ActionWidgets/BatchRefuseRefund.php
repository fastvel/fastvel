<?php
/**
 * Created by Cartman Chen <thrall.chen@gmail.com>.
 * Author: 陈章--大官人
 * Github: https://github.com/imdgr
 */

namespace Imdgr886\Order\Admin\ActionWidgets;

use Encore\Admin\Actions\BatchAction;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form\Field\Html;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Imdgr886\Order\Events\RefundApprovedEvent;
use Imdgr886\Order\Jobs\RefundJob;
use Imdgr886\Order\Models\OrderRefund;

/**
 * Class ApproveRefund
 * 批量审核退款
 */
class  BatchRefuseRefund extends BatchAction
{
    protected $selector = '.batch-post-refuse-refund';
    public $name = '批量拒绝';

    public function handle(Collection $collection)
    {
        $refundIds = $collection->pluck('id')->toArray();
        $successRows = 0;
        if ($refundIds) {
            $successRows = OrderRefund::whereIn('id', $refundIds)
                ->where('status', OrderRefund::PENDING)
                ->update([
                    'status' => OrderRefund::REFUSED,
                    'approved_by' => Admin::user()->id,
                    'approved_at' => now()
                ]);
        }

        foreach ($collection as $model) {
            $model->refresh();
            $model->fireOrderStatusUpdate();
        }

        return $this->response()->success("{$successRows} 条退款操作成功！")->refresh();
    }

    public function dialog()
    {
        $this->confirm("待审核的退款单将会被拒绝");
    }

    public function html()
    {
        return "<a class='batch-post-refuse-refund btn btn-sm btn-danger'><i class='fa fa-cross'></i>{$this->name}</a>";
    }
}
