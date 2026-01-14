<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tb_restaurants', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->string('address');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_approved')->default(false);
            $table->time('open_time')->nullable();
            $table->time('close_time')->nullable();
            $table->float('rating', 2, 1)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->foreign('created_by')->references('id')->on('tb_users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_restaurants', function (Blueprint $table) {
            $table->dropColumn([
                'name',
                'description',
                'address',
                'latitude',
                'longitude',
                'is_active',
                'is_approved',
                'open_time',
                'close_time',
                'rating',
                'created_by'
            ]);
        });
    }
};
