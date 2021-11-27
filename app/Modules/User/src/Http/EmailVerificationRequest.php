<?php

namespace Imdgr886\User\Http;

use Imdgr886\User\Models\User;

class EmailVerificationRequest extends \Illuminate\Foundation\Auth\EmailVerificationRequest
{
    public function authorize()
    {
        $this->setUserResolver(function () {
            $user = User::find( $this->route('id'));
            return $user;
        });

        if (! hash_equals((string) $this->route('hash'),
            sha1($this->user()->getEmailForVerification()))) {
            return false;
        }

        return true;
    }
}
