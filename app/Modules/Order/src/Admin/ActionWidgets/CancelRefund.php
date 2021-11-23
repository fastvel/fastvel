<?php

namespace Imdgr886\Order\Admin\ActionWidgets;

use Encore\Admin\Actions\RowAction;
use Encore\Admin\Facades\Admin;
use Illuminate\Database\Eloquent\Model;
use Imdgr886\Order\Jobs\RefundJob;
use Imdgr886\Order\Models\OrderRefund;

class CancelRefund extends RowAction
{
    public $name = '取消';

    public function handle(Model $model)
    {
        // 审核通过的、未审核、失败的，可以取消
        if ($model->canCancel()) {
            $model->status = OrderRefund::CANCELED;
            $model->save();
            return $this->response()->success('取消成功')->refresh();
        } else {
            return $this->response()->error('当前退款不可取消');
        }
    }

    public function authorize($user, $model)
    {
        return $user->can('refund');
    }

//    public function render()
//    {
//        // 只有审核通过的退款单才能发起退款
//        if (Admin::user()->can('refund')) {
//            $url = 'refund/' . $this->getKey();
//            return "<a target='_target' href='{$url}'>{$this->name()}</a>";
//        }
//
//    }
//
//    public function __toString()
//    {
//        return $this->render();
//    }

}
