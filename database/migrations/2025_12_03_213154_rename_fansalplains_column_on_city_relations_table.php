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
        Schema::table('city_relations', function (Blueprint $table) {
            $table->renameColumn('fansalplains', 'fansal_plains');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('city_relations', function (Blueprint $table) {
            $table->renameColumn('fansal_plains', 'fansalplains');
        });
    }
};
