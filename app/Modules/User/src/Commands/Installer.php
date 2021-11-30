<?php

namespace Imdgr886\User\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class Installer extends Command
{
    protected $signature = 'user:install';

    public function handle()
    {
        $this->callSilent("vendor:publish", ['--provider' => 'Tymon\JWTAuth\Providers\LaravelServiceProvider']);
        $this->callSilent("vendor:publish", ['--tag' => 'user']);
        $this->callSilent("jwt:secret");
    }
}
