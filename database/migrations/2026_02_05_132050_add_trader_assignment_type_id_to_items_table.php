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
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('trader_assignment_type');

            $table->unsignedInteger('trader_assignment_type_id')
                ->nullable()
                ->after('item_id');

            $table->foreign('trader_assignment_type_id')
                ->references('id')
                ->on('trader_assignment_types')
                ->onDelete('set null')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->string('trader_assignment_type');
            $table->dropForeignKeyIfExists(['trader_assignment_type_id']);
            $table->dropColumn('trader_assignment_type_id');
        });
    }
};
