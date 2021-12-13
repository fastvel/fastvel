<?php
/**
 * Created by Cartman Chen <thrall.chen@gmail.com>.
 * Author: 陈章--大官人
 * Github: https://github.com/imdgr
 */

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Routing\Controller;

class ShopController extends Controller
{
    public function list()
    {
        return auth('api')->user()->shops()->paginate();
    }
}
