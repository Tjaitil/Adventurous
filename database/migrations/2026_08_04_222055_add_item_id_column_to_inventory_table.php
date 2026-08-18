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
        Schema::dropIfExists('inventory');

        Schema::create('inventory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('item_id');
            $table->foreign('item_id')->references('item_id')->on('items')->cascadeOnDelete();
            $table->integer('amount');

            $table->unique(['user_id', 'item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('inventory');

        Schema::create('inventory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('item');
            $table->integer('amount');

            $table->unique(['user_id', 'item']);
        });
    }
};
