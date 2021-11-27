<?php

namespace Imdgr886\User\Http\Controllers;

use Illuminate\Routing\Controller;

class ResetPasswordController extends Controller
{
    public function viaPassword()
    {
        if (! hash_equals((string) $this->route('id'),
            (string) $this->user('web')->getKey())) {
            return false;
        }

        if (! hash_equals((string) $this->route('hash'),
            sha1($this->user('web')->getEmailForVerification()))) {
            return false;
        }

        return true;
    }

    public function viaEmail()
    {

    }

    public function viaMobile()
    {

    }
}
