<?php

namespace Imdgr886\User\Http\Controllers;

use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;
use Imdgr886\User\Http\EmailVerificationRequest;
use Imdgr886\User\Models\User;

class VerifyMobileController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws ValidationException
     */
    public function verifyMobile(Request $request)
    {
        $request->validate([
            'mobile' => ['required', 'phone:CN'],
            'verify_code' => ['required', 'verify_code']
        ]);
        $mobile = $request->get('mobile');
        if (!User::where(['mobile' => $mobile])->exist()) {
            $exception = ValidationException::withMessages([
                'mobile' => '用户未注册'
            ]);
            throw $exception;
        }
        Cache::put("mobile.{$mobile}.verified_at", time(), now()->addMinutes(30));
        return response()->json(['success' => true]);
    }
}
