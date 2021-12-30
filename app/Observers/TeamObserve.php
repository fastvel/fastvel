<?php

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;

class TeamObserve
{
    public function creating(Model $model) {
       
            $model->team_id = auth('api')->user()->current_team_id;
        
    }
}
