<?php

namespace Imdgr886\User\Http\Controllers;

use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Routing\Controller;
use Imdgr886\User\Http\EmailVerificationRequest;
use Imdgr886\User\Models\User;

class VerifyEmailController extends Controller
{
    public function verifyEmail(EmailVerificationRequest $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(RouteServiceProvider::HOME.'?verified=1');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

    }
}
