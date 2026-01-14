<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tb_orders', function (Blueprint $table) {
            $table->increments('order_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('restaurant_id');
            $table->string('order_number', 50)->unique();
            $table->decimal('total_amount', 10, 2);
            $table->string('delivery_address', 255);
            $table->string('status', 50);
            $table->string('payment_status', 50);
            $table->string('payment_method', 50);
            $table->timestamp('ordered_at');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('tb_users')->onDelete('cascade');
            $table->foreign('restaurant_id')->references('id')->on('tb_restaurants')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_orders');
    }
};
