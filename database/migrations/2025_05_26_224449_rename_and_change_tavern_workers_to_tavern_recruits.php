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
        Schema::dropIfExists('tavern_workers');

        Schema::create('tavern_recruits', function (Blueprint $table) {
            $table->id();
            $table->string('location');
            $table->integer('price');
            $table->integer('level');
            $table->unsignedBigInteger('type_id');
            $table->unsignedBigInteger('user_id');

            $table->timestamps();
            $table->timestamp('recruited_at')->nullable();

            $table->foreign('type_id')->references('id')->on('tavern_recruit_types')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

    }
};
