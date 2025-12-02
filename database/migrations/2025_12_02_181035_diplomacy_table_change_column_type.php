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
        Schema::table('diplomacy', function (Blueprint $table) {
            $table->float('hirtam')->default(1.0)->change();
            $table->float('pvitul')->default(1.0)->change();
            $table->float('khanz')->default(1.0)->change();
            $table->float('ter')->default(1.0)->change();
            $table->float('fansalplains')->default(1.0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('diplomacy', function (Blueprint $table) {
            $table->string('hirtam')->change();
            $table->string('pvitul')->change();
            $table->string('khanz')->change();
            $table->string('ter')->change();
            $table->string('fansalplains')->change();
        });
    }
};
