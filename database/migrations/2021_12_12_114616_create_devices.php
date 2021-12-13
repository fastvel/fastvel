<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDevices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('provider')->comment('提供商');
            $table->string('instance_id')->nullable()->comment('提供商的实例 id');
            $table->string('ip');
            $table->string('proxy_type');
            $table->string('proxy_port');
            $table->string('proxy_user');
            $table->string('proxy_pass');
            $table->string('remote_port')->nullable()->comment('远程桌面端口');
            $table->string('remote_user')->nullable()->comment('远程桌面用户');
            $table->string('remote_pass')->nullable()->comment('远程桌面密码');
            $table->string('status')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('devices');
    }
}
