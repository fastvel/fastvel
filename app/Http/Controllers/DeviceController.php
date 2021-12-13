<?php
/**
 * Created by Cartman Chen <thrall.chen@gmail.com>.
 * Author: 陈章--大官人
 * Github: https://github.com/imdgr
 */

namespace App\Http\Controllers;

use App\DeviceProvider\Type;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DeviceController extends Controller
{
    /**
     * 创建自己搭建的代理设备
     * @return void
     */
    public function createSelfHost(Request $request)
    {
        $request->validate([
            'ip' => ['required', 'ip'],
            'proxy_port' => ['required', 'numeric'],
            'proxy_type' => ['required'],
            'proxy_user' => ['string'],
            'proxy_password' => ['string'],
        ]);

        return Device::create(array_merge(
            $request->all(),
            ['type' => Type::SelfHost]
        ));
    }

    public function all()
    {

    }


}
