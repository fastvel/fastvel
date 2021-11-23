<?php

return [
    'timeout' => 5.0,



    // 默认发送配置
    'default' => [
        // 网关调用策略，默认：顺序调用
        'strategy' => \Overtrue\EasySms\Strategies\OrderStrategy::class,
//        'strategy' => \Overtrue\EasySms\Strategies\RandomStrategy::class,

        // 各场景可用的发送网关
        'gateways' => [
            // 默认
            'default' => [
                'qcloud'
            ],
            //登录验证
            'login-verify' => [
                'aliyun',
                'qcloud'
            ],
            // 充值密码
            'reset-password' => [
                'qcloud',
                'aliyun'
            ],
        ],
    ],
    // 可用的网关配置, see: https://github.com/overtrue/easy-sms
    'gateways' => [
        'aliyun' => [
            'access_key_id' => env('ALIYUN_ACCESS_ID'),
            'access_key_secret' => env('ALIYUN_ACCESS_KEY'),
            'sign_name' => env('ALIYUN_SMS_SIGN'),
        ],
        'qcloud' => [
            'sdk_app_id' => env('TENCENT_SMS_APP_ID'),
            'app_key' => env('TENCENT_SMS_APP_KEY'),
            'sign_name' => env('ALIYUN_SMS_SIGN'),
        ],


        'errorlog' => [
            'file' => storage_path('easy-sms.log'),
        ],
    ],
    'templates' => [
        'login-verify' => [
            'aliyun' => [
                'template_id' => 'SMS_215215243',
                'content' => "验证码{code}，您正在登录，若非本人操作，请勿泄露。"
            ],
            'qcloud' => [
                'template_id' => '1083435',
                'content' => "验证码为{code}，您正在登录，若非本人操作，请勿泄露。"
            ]
        ],
        'reset-password' => [
            'qcloud' => [
                'template_id' => '1096882',
                'content' => "验证码{code}，您正在尝试修改登录密码，请妥善保管账户信息。"
            ],
            'aliyun' => [
                'template_id' => 'SMS_215215240',
                'content' => "验证码{code}，您正在尝试修改登录密码，请妥善保管账户信息。"
            ]
        ],
    ]
];
