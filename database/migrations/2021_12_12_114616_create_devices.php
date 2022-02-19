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
            $table->foreignId('team_id');
            $table->string('ip')->nullable();
            $table->string('proxy_type')->nullable();
            $table->string('proxy_port')->nullable();
            $table->string('proxy_user')->nullable();
            $table->string('proxy_pass')->nullable();
            $table->string('status')->nullable();
            $table->timestampTz('expires_at')->nullable()->comment('到期日期');
            $table->string('provider')->nullable()->comment('设备提供商');
            $table->string('instance_id')->nullable()->comment('提供商的设备 id');
            $table->string('os')->nullable()->comment('操作系统');
            $table->string('instance_model')->nullable()->comment('机型');
            $table->string('region_code')->nullable()->comment('地区');
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
