<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            DB::statement('CREATE TABLE items_backup AS SELECT * FROM items');
            Schema::drop('items');

            Schema::create('items', function (Blueprint $table) {
                $table->integer('item_id')->primary();
                $table->string('name')->unique('items_name_unique');
                $table->integer('store_value');
                $table->integer('in_game');
                $table->integer('towhar_rate');
                $table->integer('golbak_rate');
                $table->integer('snerpiir_rate');
                $table->integer('cruendo_rate');
                $table->integer('pvitul_rate');
                $table->integer('khanz_rate');
                $table->integer('ter_rate');
                $table->integer('krasnur_rate');
                $table->integer('hirtam_rate');
                $table->integer('fansal_plains_rate');
                $table->integer('tasnobil_rate');
                $table->string('trader_assignment_type');
                $table->integer('adventure_requirement');
                $table->string('adventure_requirement_difficulty');
                $table->string('adventure_requirement_role');
            });

            DB::statement('INSERT INTO items SELECT * FROM items_backup');
            DB::statement('DROP TABLE items_backup');
        } else {
            Schema::table('items', function (Blueprint $table) {
                $table->primary('item_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            // For SQLite, recreate the table without the primary key
            DB::statement('CREATE TABLE items_backup AS SELECT * FROM items');
            Schema::drop('items');

            Schema::create('items', function (Blueprint $table) {
                $table->integer('item_id');
                $table->string('name');
                $table->integer('store_value');
                $table->integer('in_game');
                $table->integer('towhar_rate');
                $table->integer('golbak_rate');
                $table->integer('snerpiir_rate');
                $table->integer('cruendo_rate');
                $table->integer('pvitul_rate');
                $table->integer('khanz_rate');
                $table->integer('ter_rate');
                $table->integer('krasnur_rate');
                $table->integer('hirtam_rate');
                $table->integer('fansal_plains_rate');
                $table->integer('tasnobil_rate');
                $table->string('trader_assignment_type');
                $table->integer('adventure_requirement');
                $table->string('adventure_requirement_difficulty');
                $table->string('adventure_requirement_role');
            });

            DB::statement('INSERT INTO items SELECT * FROM items_backup');
            DB::statement('DROP TABLE items_backup');
        } else {
            // MySQL can directly drop the primary key
            Schema::table('items', function (Blueprint $table) {
                $table->dropPrimary();
            });
        }
    }
};
