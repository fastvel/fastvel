<?php

namespace Imdgr886\User\Http\Controllers;

use Illuminate\Routing\Controller;
use Imdgr886\Team\TeamServiceProvider;
use Imdgr886\User\Models\User;

class ProfileController extends Controller
{
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        /**
         * @var $user User
         */
        $user = auth('api')->user();

        return response()->json($user);
    }
}
