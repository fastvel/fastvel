<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class OrderFullfils extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('fulfill_status')->comment('履行状态: pending, operating, fulfilled, failed');
        });

        Schema::create('order_fulfills', function (Blueprint $table){
            $table->id();
            $table->foreignId('order_id');
            $table->foreignId('device_id');
            $table->string('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table){
            $table->dropColumn('fulfill_status');
        });

        Schema::dropIfExists('order_fulfills');
    }
}
