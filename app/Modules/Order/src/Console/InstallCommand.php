<?php

/**
 * Created by Cartman Chen <thrall.chen@gmail.com>.
 * Author: 陈章--大官人
 * Github: https://github.com/imdgr
 */

namespace Imdgr886\Order\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Route;

class InstallCommand extends Command
{
    protected $signature = 'order:install';

    public function handle()
    {
        $this->callSilent("vendor:publish", ['--tag' => 'laravel-pay']);
        (new Filesystem)->copyDirectory(__DIR__ . '/../../migrations/', database_path('migrations'));
        (new Filesystem)->copyDirectory(__DIR__ . '/../../views/', resource_path('views'));
    }
}
