<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tb_restaurant_category_map', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('restaurant_id');
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();

            $table->foreign('restaurant_id')->references('id')->on('tb_restaurants')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('tb_restaurant_categories')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('tb_users')->onDelete('set null');
            $table->unique(['restaurant_id', 'category_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_restaurant_category_map');
    }
};
