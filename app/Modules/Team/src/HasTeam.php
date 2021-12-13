<?php

namespace Imdgr886\Team;

use Imdgr886\Team\Models\Membership;
use Imdgr886\Team\Models\Team;

trait HasTeam
{
    /**
     * Determine if the given team is the current team.
     *
     * @param  mixed  $team
     * @return bool
     */
    public function isCurrentTeam($team)
    {
        return $team->id === $this->currentTeam->id;
    }

    /**
     * Get the current team of the user's context.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currentTeam()
    {
        if (is_null($this->current_team_id) && $this->id) {
            $this->switchTeam($this->personalTeam());
        }

        return $this->belongsTo(Team::class, 'current_team_id');
    }

    public function getCurrentTeamAttribute()
    {
        return $this->currentTeam()->first();
    }

    /**
     * Switch the user's context to the given team.
     *
     * @param  mixed  $team
     * @return bool
     */
    public function switchTeam($team)
    {
        if (! $this->belongsToTeam($team)) {
            return false;
        }

        $this->forceFill([
            'current_team_id' => $team->id,
        ])->save();

        $this->setRelation('currentTeam', $team);

        return true;
    }

    /**
     * 获取用户所有自有的团队和所属团队.
     *
     * @return \Illuminate\Support\Collection
     */
    public function allTeams()
    {
        return $this->ownedTeams->merge($this->teams)->sortByDesc('id');
    }

    /**
     * 获取用户所有的团队.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ownedTeams()
    {
        return $this->hasMany(Team::class);
    }

    /**
     * 获取用户所属的所有团队.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function teams()
    {
        return $this->belongsToMany(Team::class, Membership::class)
            ->withPivot('role')
            ->withTimestamps()
            ->as('membership');
    }

    /**
     * Get the user's "personal" team.
     *
     * @return Team
     */
    public function personalTeam()
    {
        return $this->ownedTeams->where('personal_team', true)->first();
    }

    /**
     * 判断用户是否是指定的团队的拥有者.
     *
     * @param  mixed  $team
     * @return bool
     */
    public function ownsTeam($team)
    {
        if (is_null($team)) {
            return false;
        }

        return $this->id == $team->{$this->getForeignKey()};
    }

    /**
     * 判断用户是否属于某个团队
     *
     * @param  mixed  $team
     * @return bool
     */
    public function belongsToTeam($team)
    {
        return $this->teams->contains(function ($t) use ($team) {
                return $t->id === $team->id;
            }) || $this->ownsTeam($team);
    }

    /**
     * Get the role that the user has on the team.
     *
     * @param  mixed  $team
     * @return \Laravel\Jetstream\Role|null
     */
    public function teamRole($team)
    {
        if ($this->ownsTeam($team)) {
            return new OwnerRole;
        }

        if (! $this->belongsToTeam($team)) {
            return;
        }

        $role = $team->users
            ->where('id', $this->id)
            ->first()
            ->membership
            ->role;

        return $role ? Jetstream::findRole($role) : null;
    }

    /**
     * Determine if the user has the given role on the given team.
     *
     * @param  mixed  $team
     * @param  string  $role
     * @return bool
     */
    public function hasTeamRole($team, string $role)
    {
        if ($this->ownsTeam($team)) {
            return true;
        }

        return $this->belongsToTeam($team) && optional(Jetstream::findRole($team->users->where(
                'id', $this->id
            )->first()->membership->role))->key === $role;
    }

    /**
     * Get the user's permissions for the given team.
     *
     * @param  mixed  $team
     * @return array
     */
    public function teamPermissions($team)
    {
        if ($this->ownsTeam($team)) {
            return ['*'];
        }

        if (! $this->belongsToTeam($team)) {
            return [];
        }

        return (array) optional($this->teamRole($team))->permissions;
    }

    /**
     * Determine if the user has the given permission on the given team.
     *
     * @param  mixed  $team
     * @param  string  $permission
     * @return bool
     */
    public function hasTeamPermission($team, string $permission)
    {
        if ($this->ownsTeam($team)) {
            return true;
        }

        if (! $this->belongsToTeam($team)) {
            return false;
        }

        if (in_array(HasApiTokens::class, class_uses_recursive($this)) &&
            ! $this->tokenCan($permission) &&
            $this->currentAccessToken() !== null) {
            return false;
        }

        $permissions = $this->teamPermissions($team);

        return in_array($permission, $permissions) ||
            in_array('*', $permissions) ||
            (Str::endsWith($permission, ':create') && in_array('*:create', $permissions)) ||
            (Str::endsWith($permission, ':update') && in_array('*:update', $permissions));
    }
}
