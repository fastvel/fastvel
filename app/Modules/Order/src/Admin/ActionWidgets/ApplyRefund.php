<?php

namespace Imdgr886\Order\Admin\ActionWidgets;

use Encore\Admin\Actions\RowAction;
use Encore\Admin\Facades\Admin;
use Illuminate\Database\Eloquent\Model;

class ApplyRefund extends RowAction
{
    public $name = '申请退款';

    public function handle(Model $model)
    {
        // return $this->response()->success('Success message.')->refresh();
    }

    public function render()
    {
        // 只有审核通过的退款单才能发起退款
        if (Admin::user()->can('refund')) {
            $url = 'refund/' . $this->getKey();
            return "<a target='_target' href='{$url}'>{$this->name()}</a>";
        }

    }

    public function __toString()
    {
        return $this->render();
    }

}
