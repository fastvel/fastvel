<?php

namespace Imdgr886\Order\Admin\ActionWidgets;

use Encore\Admin\Actions\RowAction;
use Encore\Admin\Facades\Admin;
use Illuminate\Database\Eloquent\Model;
use Imdgr886\Order\Jobs\RefundJob;
use Imdgr886\Order\Models\OrderRefund;

class ExecuteRefund extends RowAction
{
    public $name = '执行退款';

    public function handle(Model $model)
    {
        if ($model->canRefund()) {
            if ($model->status != OrderRefund::REFUNDING) {
                $model->status = OrderRefund::REFUNDING;
                $model->save();
            }
            dispatch(new RefundJob($model));
            return $this->response()->success('正在退款。')->refresh();
        } else {
            return $this->response()->error('执行失败，当前状态不可执行退款。');
        }

    }

    public function authorize($user, $model)
    {
        return $user->can('refund');
    }

}
