<?php

namespace Imdgr886\Team\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Imdgr886\Team\Models\Team;
use Imdgr886\Team\Models\TeamInvitation;
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

    /**
     * 邮件邀请的详情
     * @param TeamInvitation $invitation
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(TeamInvitation $invitation)
    {
        $invitation->append(['user', 'team']);
        return response()->json($invitation);
    }

    public function accept(Request $request, TeamInvitation $invitation)
    {
        // Gate::forUser($invitation->team->user)->authorize('addTeamMember', $invitation->team);
        $request->validate([
            'email' => ['required', 'email', 'exists:users'],
            'role' => ['required', 'string'],
        ]);
        if ($invitation->team->hasUserWithEmail($request->email)) {
            throw ValidationException::withMessages([
                'email' => '此用户已经加入团队'
            ]);
        }
    }

    /**
     * 删除邀请
     * @param Request $request
     * @param TeamInvitation $invitation
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, TeamInvitation $invitation)
    {
        if (! Gate::forUser($request->user())->check('removeTeamMember', $invitation->team)) {
            throw new AuthorizationException();
        }

        $invitation->delete();

        return response()->json([
            'success' => true,
            'message' => '删除成功'
        ]);
    }

}
