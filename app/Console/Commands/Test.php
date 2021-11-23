<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Imdgr886\Sms\Sms;
use Imdgr886\Snowflake\Facades\Snowflake as FacadesSnowflake;
use Imdgr886\Snowflake\Snowflake;

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
        //$sn = app()->make(Snowflake::class, ['startTime' => '2021-11-18 00:00:00']);
        $sms = new Sms();
        $sms->send('login-verify', 18680672254, ['code' => 766, 'expires' => 5]);

    }
}
