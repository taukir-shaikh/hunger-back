<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tb_order_status_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('order_id');
            $table->string('old_status', 50);
            $table->string('new_status', 50);
            $table->string('changed_by', 50);
            $table->timestamp('changed_at');
            $table->timestamps();

            $table->foreign('order_id')->references('order_id')->on('tb_orders')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_order_status_history');
    }
};
