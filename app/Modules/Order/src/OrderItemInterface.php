<?php
/**
 * Created by Cartman Chen <thrall.chen@gmail.com>.
 * Author: 陈章--大官人
 * Github: https://github.com/imdgr
 */

namespace Imdgr886\Order;

interface OrderItemInterface
{
    public function getPrice();

    public function getName();

    public function getItemAmount($qty = 1);

    public function getPrimaryKeyValue();
}
