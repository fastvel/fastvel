<?php
/**
 * Created by Cartman Chen <thrall.chen@gmail.com>.
 * Author: 陈章--大官人
 * Github: https://github.com/imdgr
 */

namespace Imdgr886\Team\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class JoinTeamController extends Controller
{
    /**
     * 申请加入团队
     * @param Request $request
     * @return void
     */
    public function join(Request $request)
    {
        $request->validate([
            'team_id' => ['requried', 'exists:teams,id']
        ]);
    }
}
