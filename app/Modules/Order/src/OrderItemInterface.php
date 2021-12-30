<?php
/**
 * Created by Cartman Chen <thrall.chen@gmail.com>.
 * Author: 陈章--大官人
 * Github: https://github.com/imdgr
 */

namespace Imdgr886\Order;

interface OrderItemInterface
{
    public function getPrice(array $options = []);

    public function getName(array $options = []);

    public function getProductAmount($qty = 1, array $options = []);
}
