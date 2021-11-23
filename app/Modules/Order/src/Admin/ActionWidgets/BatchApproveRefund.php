<?php
/**
 * Created by Cartman Chen <thrall.chen@gmail.com>.
 * Author: 陈章--大官人
 * Github: https://github.com/imdgr
 */

namespace Imdgr886\Order\Admin\ActionWidgets;

use Encore\Admin\Actions\BatchAction;
use Encore\Admin\Facades\Admin;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Imdgr886\Order\Events\RefundApprovedEvent;
use Imdgr886\Order\Jobs\RefundJob;
use Imdgr886\Order\Models\OrderRefund;

/**
 * Class ApproveRefund
 * 批量审核退款
 */
class  BatchApproveRefund extends BatchAction
{
    protected $selector = '.batch-post-approve-refund';
    public $name = '批量审核';

    public function handle(Collection $collection)
    {
        // 选中的记录的 id
        $refundIds = $collection->pluck('id')->toArray();
        $successRows = 0;
        // 是否立即退款
        $after =request()->get('after_approved', []);
        $refundNow = in_array('refund', $after);
        if ($refundIds) {
            $successRows = OrderRefund::whereIn('id', $refundIds)
                ->where('status', OrderRefund::PENDING)
                ->update([
                    'status' => $refundNow ? OrderRefund::REFUNDING : OrderRefund::APPROVED,
                    'approved_by' => Admin::user()->id,
                    'approved_at' => now()
                ]);
        }

        foreach ($collection as $model) {

            if ($model->status == 'pending') {
                event(new RefundApprovedEvent($model));
                if ($refundNow) dispatch(new RefundJob($model));
            }
        }

        return $this->response()->success("{$successRows} 条退款操作成功！")->refresh();
    }

    public function form()
    {
        $this->checkbox('after_approved', '审核通过后：')->options(['refund' => '立即退款']);
    }

    public function html()
    {
        return "<a class='batch-post-approve-refund btn btn-sm btn-danger'><i class='fa fa-check'></i>{$this->name}</a>";
    }
}
