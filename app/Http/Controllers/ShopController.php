<?php
/**
 * Created by Cartman Chen <thrall.chen@gmail.com>.
 * Author: 陈章--大官人
 * Github: https://github.com/imdgr
 */

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;

class ShopController extends Controller
{
    public function list()
    {
        return auth('api')->user()->shops()->paginate();
    }

    public function createCheck(Request $request)
    {
        $request->validate([
            'team_id' => ['required', 'exists:teams,id'],
            'name' => [
                'required',
                Rule::unique('shops')->where('team_id', $request->get('team_id'))
            ],
            'platform' => ['required', 'string'],
            'account' => ['nullable'],
            'password' => ['nullable'],
            'tag' => ['array']

        ], [], [
            'team_id' => '团队',
            'name' => '店铺名称',
            'platform' => '所属平台',
            'account' => '登录账号',
            'password' => '登录密码'
        ]);
        Shop::create(array_merge($request->all(), ['device_id' => 1]));
    }

    public function store(Request $request)
    {
        $this->createCheck($request);
    }
}
