<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Imdgr886\Sms\Sms;
use Imdgr886\Snowflake\Facades\Snowflake as FacadesSnowflake;
use Imdgr886\Snowflake\Snowflake;
use Imdgr886\User\Models\Oauth;
use Imdgr886\User\Models\User;
use Imdgr886\Wechat\Events\ScanLoginEvent;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        //Oauth::query()->where(['openid' => 'oyw8o63Azs6LfSKR-PrLauITWhQw', 'platform' => Oauth::WECHAT_MP])->first();
        event(new ScanLoginEvent(User::first(), '5597230415872'));
        return;
        $v = Validator::make(['mobiles' => 18680672254, 'verify' => 195793], [
            'verify' => 'verify_code:mobiles,reset'
        ]);
        dump($v->validate());
        return;
        //$sn = app()->make(Snowflake::class, ['startTime' => '2021-11-18 00:00:00']);
        $sms = new Sms();
        if ($sms->sendVerify(18680672254, 'reset')) {
            $this->info('success');
        } else {
            $this->error('failed');
        }

    }
}
