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
        $perPage = $request->get('per_page');
        $shopes = Shop::query()
            ->with('device')
            ->orderByDesc('created_at')->paginate($perPage);
        return response()->json($shopes);
    }

    /**
     * 团队设备的总数
     * @param Request $request
     * @param Team $team
     * @return JsonResponse
     */
    public function total(Team $team)
    {
        $count = Shop::query()->where('team_id', $team->id)->count();
        return response()->json([
            'success'=> true,
            'total' => $count
        ]);
    }

    public function createCheck(Request $request)
    {
        $request->validate([
            //'team_id' => ['required', 'exists:teams,id'],
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

    public function store(Request $request, Team $team)
    {
        $this->createCheck($request);
        $data = $request->all();
        $data['team_id'] = $team->id;
        $shop = Shop::create($data);
        return response()->json($shop);
    }

    /**
     * 用户所有可用的店铺
     * @return JsonResponse
     */
    public function allShopes(Request $request, Team $team)
    {
        $perPage = $request->get('per_page');
        $shopes = Shop::query()
            ->with('device')
            ->orderByDesc('created_at')->paginate($perPage);

        return response()->json($shopes->toArray());
    }
}
