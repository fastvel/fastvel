<?php

namespace Imdgr886\User\Http\Controllers;

use Illuminate\Routing\Controller;

class ProfileController extends Controller
{
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth('api')->user());
    }
}
