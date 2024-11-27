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
        Schema::dropIfExists('diplomacy');

        Schema::create('diplomacy', function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->unsignedBigInteger('user_id');
            $table->string('hirtam');
            $table->string('pvitul');
            $table->string('khanz');
            $table->string('ter');
            $table->string('fansalplains');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // no need to reverse this migration
    }
};
