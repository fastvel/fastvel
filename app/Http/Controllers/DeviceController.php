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
use Imdgr886\Team\Models\Team;

class DeviceController extends Controller
{
    /**
     * 创建自己搭建的代理设备
     * @return void
     */
    public function createSelfHost(Request $request, Team $team)
    {
        $request->validate([
            'ip' => ['required', 'ip', 'unique:devices,ip'],
            'proxy_port' => ['required', 'numeric'],
            // 'team_id' => ['required', 'exists:teams,id'],
            'proxy_type' => ['required'],
            'proxy_user' => ['string'],
            'proxy_pass' => ['string'],
        ], [
            'attributes' => [
                'ip' => '代理地址',
                'team_id' => '团队',
                'proxy_port' => '代理端口',
                'proxy_type' => '代理类型',
                'proxy_user' => '认证用户',
                'proxy_pass' => '认证密码',
            ]
        ]);

        return Device::create(array_merge(
            $request->all(),
            ['provider' => Type::SelfHost, 'team_id' => $team->id]
        ));
    }

    public function all(Request $request, Team $team)
    {
        $query = Device::query()->where('team_id', $team->id)->orderByDesc('created_at');
        return $query->paginate();
    }

    public function checkProxy()
    {

    }

    /**
     * 可以绑定的设备
     * @param Request $request
     * @return void
     */
    public function canBindDevices(Request $request, Team $team)
    {
        $request->validate([
            'team_id' => ['exists:teams,id'],
            'platform' => ['required']
        ]);
    }

    /**
     * 团队的设备总数
     * @param Request $request
     * @param Team $team
     * @return \Illuminate\Http\JsonResponse
     */
    public function total(Team $team)
    {
        $count = Device::query()->where('team_id', $team->id)->count();
        return response()->json([
            'success'=> true,
            'total' => $count
        ]);
    }

}
