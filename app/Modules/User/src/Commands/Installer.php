<?php

namespace Imdgr886\User\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class Installer extends Command
{
    protected $signature = 'user:install';

    public function handle()
    {
        $this->call("vendor:publish", ['--provider' => 'Tymon\JWTAuth\Providers\LaravelServiceProvider']);
        $this->call("vendor:publish", ['--tag' => 'user']);
        $this->call("jwt:secret", ['--force' => true]);
    }
}
