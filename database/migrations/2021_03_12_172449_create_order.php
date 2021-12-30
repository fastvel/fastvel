<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users');
            $table->decimal('items_total')->default(0)->comment('原始总金额');
            $table->decimal('discount_amount')->default(0)->comment('优惠金额');
            $table->decimal('order_amount')->default(0)->comment('订单应支付金额');
            $table->decimal('paid_amount')->default(0)->comment('已支付金额');
            $table->string('invoice_no')->nullable()->comment('发票号');
            $table->string('status')->default('pending');
            $table->timestampTz('paid_at')->nullable();
            $table->ipAddress('ip')->nullable();
            $table->string('source')->nullable()->comment('订单来源');
            $table->timestampsTz();
            $table->softDeletes();
        });

        // 订单历史
        Schema::create('order_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->references('id')->on('orders')->cascadeOnDelete();
            $table->string('order_status');
            $table->text('comment')->nullable();
            $table->timestampTz('created_at');
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->foreignId('order_id')->references('id')->on('orders')->cascadeOnDelete();
            $table->morphs('product');
            $table->integer('qty')->default(1)->comment('数量');
            $table->decimal('price')->default(0);
            $table->decimal('total')->default(0);
            $table->string('name')->nullable()->comment('名称');
            $table->json('options')->nullable()->comment('其他参数与选项');
        });

        // 交易流水表
        Schema::create('order_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->references('id')->on('orders')->cascadeOnDelete();
            $table->decimal('order_amount')->comment('付款金额');
            $table->decimal('paid_amount')->default(0);
            $table->string('payment_gateway')->nullable()->comment('支付平台');
            $table->string('payment_method')->nullable()->comment('付款方式');
            $table->string('trade_no')->nullable()->comment('支付平台交易号');
            $table->timestampTz('paid_at')->nullable()->comment('支付时间');
            $table->ipAddress('ip')->nullable();
            $table->timestampsTz();
        });

        // 交易结果通知表
        Schema::create('order_transaction_notifies', function (Blueprint $table) {
            $table->foreignId('transaction_id')->references('id')->on('order_transactions')->cascadeOnDelete();
            $table->string('event')->nullable();
            $table->text('result')->nullable();
            $table->text('data')->nullable();
            $table->timestampTz('created_at');
        });

        // 退款表
        Schema::create('order_refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->references('id')->on('orders')->cascadeOnDelete();
            $table->decimal('refund_amount')->default(0)->comment('退款金额');
            $table->text('reason')->nullable()->comment('退款原因');
            $table->unsignedBigInteger('transaction_id');
            $table->string('status')
                ->default('pending')
                ->comment('pending：待审批，approved：审批通过，refunding：退款中，refunded：已退款');
            $table->timestampTz('approved_at')->nullable();
            // $table->morphs('approved_by')->nullable();
            $table->text('comment', 1000)->nullable();
            $table->timestampTz('refunded_at')->nullable()->comment('退款成功时间');
            $table->morphs('apply_by');
            $table->timestampsTz();
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
        Schema::dropIfExists('order_histories');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('order_transaction_notifies');
        Schema::dropIfExists('order_transactions');
        Schema::dropIfExists('order_refunds');
        Schema::dropIfExists('orders');
    }
}
