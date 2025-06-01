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
        Schema::dropIfExists('warriors_levels_data');

        Schema::create('warriors_levels_data', function (Blueprint $table) {
            $table->integer('skill_level');
            $table->integer('next_level');
            $table->primary('skill_level');
        });
    }
};
