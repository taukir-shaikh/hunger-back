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
        Schema::table('tb_users', function (Blueprint $table) {
            $table->boolean('email_verified')->default(false);
            $table->boolean('phone_verified')->default(false);
            $table->timestamp('phone_verified_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_users', function (Blueprint $table) {
            $table->dropColumn('email_verified');
            $table->dropColumn('phone_verified');
            $table->dropColumn('phone_verified_at');
        });
    }
};
