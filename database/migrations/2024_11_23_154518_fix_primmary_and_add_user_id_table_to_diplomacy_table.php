<?php

use App\Models\Diplomacy;
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
        Schema::table('diplomacy', function (Blueprint $table) {
            $table->dropForeign('FK_diplomacy_user_id');

        });

        Schema::table('diplomacy', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement()->change();

            $table->unsignedBigInteger('user_id');
        });

        Diplomacy::all()->each(function (Diplomacy $Diplomacy) {
            $Diplomacy->user_id = User::where('username', $Diplomacy->username)->firstOrFail()->id;
            $Diplomacy->save();
        });

        Schema::table('diplomacy', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('diplomacy', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
            $table->unsignedBigInteger('id')->change();
            $table->foreign('id', 'FK_diplomacy_user_id')->references('id')->on('users')->name('FK_diplomacy_user_id');
        });
    }
};
