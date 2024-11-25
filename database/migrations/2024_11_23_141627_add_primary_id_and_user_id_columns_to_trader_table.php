<?php

use App\Models\Trader;
use App\Models\User;
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
        Schema::table('trader', function (Blueprint $table) {
            $table->dropColumn('id');
            $table->integer('cart_id')->default(1)->change();
            $table->foreign('cart_id')->references('id')->on('travelbureau_carts');

            $table->unsignedBigInteger('user_id')->after('id');
        });

        Schema::table('trader', function (Blueprint $table) {
            $table->id();
        });

        Trader::all()->each(function (Trader $trader) {
            $trader->user_id = User::where('username', $trader->username)->firstOrFail()->id;
            $trader->save();
        });
        Schema::table('trader', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trader', function (Blueprint $table) {
            $table->dropForeign(['cart_id']);
            $table->dropColumn('id');
            $table->unsignedBigInteger('id', false);
            $table->foreign('cart_id')->references('id')->on('travelbureau_carts');
        });
    }
};
