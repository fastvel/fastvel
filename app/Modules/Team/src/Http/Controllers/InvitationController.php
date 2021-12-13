<?php

namespace Imdgr886\Team\Http\Controllers;

use Illuminate\Routing\Controller;
use Imdgr886\Team\Models\Team;
use Ramsey\Uuid\Uuid;

class InvitationController extends Controller
{
    public function resetLink(Team $team)
    {
        // todo: permission
        $team->invite_token = str_replace('-', '', Uuid::uuid1()->toString());
        $team->save();
        return $team;
    }


}
