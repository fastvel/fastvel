<?php

namespace Imdgr886\Team\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Imdgr886\Team\Models\Team;
use Imdgr886\User\Models\User;
use Ramsey\Uuid\Uuid;

class TeamController extends Controller
{
    /**
     * Create a new team.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        /**
         * @var $user User
         */
        $user = auth('api')->user();
        $user->switchTeam($team = $user->ownedTeams()->create([
            'name' => $request->get('name'),
            'personal_team' => false,
            'invite_link' => str_replace('-', '', Uuid::uuid1())
        ]));

        return response()->json([
            'success' => true,
            'message' => '创建团队[' .$request->get('name') . ']成功'
        ]);
    }

    /**
     * Update the given team's name.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $teamId
     * @return \Illuminate\Http\JsonResponse
     */
    public function rename(Request $request, Team $team)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255']
        ]);
        $team->name = $request->get('name');
        $team->save();
        return response()->json($team);

//        app(UpdatesTeamNames::class)->update($request->user(), $team, $request->all());
//
//        return back(303);
    }

    /**
     * Delete the given team.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $teamId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, $teamId)
    {
        $team = Team::query()->findOrFail($teamId);

        app(ValidateTeamDeletion::class)->validate($request->user(), $team);

        $deleter = app(DeletesTeams::class);

        $deleter->delete($team);

        return $this->redirectPath($deleter);
    }

    public function switchTeam(Request $request, Team $team)
    {
        // $team = Team::query()->findOrFail($request->team_id);
        auth('api')->user()->switchTeam($team);
    }

    public function allTeams(Request $request)
    {
        return response()->json(auth('api')->user()->allTeams()->toArray());
    }
}
