<?php

namespace Imdgr886\Team\Listeners;

use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Str;
use Imdgr886\Team\Models\Team;
use Ramsey\Uuid\Uuid;

class CreatePersonalTeam
{
    public function handle(Registered $event)
    {
        if(!method_exists($event->user, 'ownedTeams')) {
            return;
        }
        $event->user->ownedTeams()->save(Team::forceCreate([
            'user_id' => $event->user->id,
            'name' => $event->user->name . "的团队",
            'personal_team' => true,
            'invite_token' => str_replace('-', '', Uuid::uuid1()->toString())
        ]));
    }
}
