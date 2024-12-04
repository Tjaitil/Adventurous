<?php

use App\Models\Inventory;
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
        Schema::table('inventory', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->string('item')->length(50)->change();
        });

        $Inventory = Inventory::all();
        $Inventory->each(function (Inventory $inventory) {
            $inventory->user_id = User::where('username', $inventory->username)->first()->id;
            $inventory->save();
        });

        Schema::table('inventory', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
            $table->dropColumn('username');

            $table->unique(['user_id', 'item']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
