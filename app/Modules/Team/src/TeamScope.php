<?php

namespace Imdgr886\Team;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TeamScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        // $builder->where('team_id', )
    }
}
