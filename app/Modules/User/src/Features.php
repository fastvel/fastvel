<?php
/**
 * Created by Cartman Chen <thrall.chen@gmail.com>.
 * Author: 陈章--大官人
 * Github: https://github.com/imdgr
 */

namespace Imdgr886\User;

class Features
{
    /**
     * 开启邮箱认证
     * @return string
     */
    public static function emailAuth(): string
    {
        return 'email_auth';
    }

    /**
     * 开启手机号认证
     * @return string
     */
    public static function mobileAuth(): string
    {
        return 'mobile_auth';
    }
}
