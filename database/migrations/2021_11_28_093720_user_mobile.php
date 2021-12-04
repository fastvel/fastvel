<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UserMobile extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('mobile', 15)->unique()->nullable()->comment('手机号');
            $table->string('email')->nullable()->change();
            $table->string('password')->nullable()->change();
            $table->string('avatar')->nullable();
        });

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
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('mobile');
            $table->dropColumn('avatar');
        });
        Schema::dropIfExists('user_oauth');
    }
}
