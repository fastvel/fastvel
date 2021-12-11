<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UserOauth extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_oauth', function (Blueprint $table) {
            $table->foreignId('user_id')->references('id')->on('users');
            $table->string('openid');
            $table->string('platform');
            $table->string('access_token')->nullable();
            $table->string('refresh_token')->nullable();
            $table->timestampTz('expires_at')->nullable();
            $table->string('unionid')->nullable();
            $table->json('raw_data')->nullable();
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_oauth');
    }
}
