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
                //'aliyun',
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
//        'aliyun' => [
//            'access_key_id' => env('ALIYUN_ACCESS_ID'),
//            'access_key_secret' => env('ALIYUN_ACCESS_KEY'),
//            'sign_name' => '大官人科技',
//        ],
        'qcloud' => [
            'sdk_app_id' => '1400053997',
            'secret_id' => env('QCLOUD_ACCESS_ID'),
            'secret_key' => env('QCLOUD_ACCESS_KEY'),
            'sign_name' => '大官人科技'
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
                'template_id' => '560918',
                'content' => "您的验证码123456，5分钟内有效。"
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
