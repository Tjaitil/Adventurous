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
        Schema::dropIfExists('trader');

        Schema::create('trader', function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->unsignedBigInteger('user_id');
            $table->integer('cart_id')->default(1);
            $table->integer('cart_amount')->default(0)->nullable(false);
            $table->integer('delivered')->default(0)->nullable(false);
            $table->timestamp('trading_countdown')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('assignment_id')->nullable(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse this migration
    }
};
