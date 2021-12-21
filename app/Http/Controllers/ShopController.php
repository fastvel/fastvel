<?php
/**
 * Created by Cartman Chen <thrall.chen@gmail.com>.
 * Author: 陈章--大官人
 * Github: https://github.com/imdgr
 */

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;
use Imdgr886\Team\Models\Team;

class ShopController extends Controller
{
    public function list(Request $request, Team $team)
    {
        $perPage = $request->get('per_page', 20);
        $shopes = Shop::query()->where('team_id', $team->id) ->paginate($perPage);

        return response()->json($shopes);
    }

    public function createCheck(Request $request)
    {
        $request->validate([
            'team_id' => ['required', 'exists:teams,id'],
            'device_id' => ['required', 'exists:devices,id'],
            'name' => [
                'required',
                Rule::unique('shops')->where('team_id', $request->get('team_id'))
            ],
            'platform' => ['required', 'string'],
            'account' => ['nullable'],
            'password' => ['nullable'],
            'tag' => ['array']

        ], [], [
            'name' => '店铺名称',
            'team_id' => '团队',
            'platform' => '所属平台',
            'account' => '登录账号',
            'password' => '登录密码'
        ]);
    }

    public function store(Request $request)
    {
        $this->createCheck($request);
        $shop = Shop::create($request->all());
        return response()->json($shop);
    }

    /**
     * 用户所有可用的店铺
     * @return JsonResponse
     */
    public function allShopes(Request $request, Team $team)
    {
        $perPage = $request->get('per_page');
        $shopes = Shop::query()->paginate($perPage);

        return response()->json($shopes);
    }
}
