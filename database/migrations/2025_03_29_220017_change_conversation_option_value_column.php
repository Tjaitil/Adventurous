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
        Schema::table('conversation_trackers', function (Blueprint $table) {
            $table->dropColumn('conversation_option_value');
            $table->json('selected_option_values')->nullable()->after('current_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversation_trackers', function (Blueprint $table) {
            $table->dropColumn('selected_option_values');
            $table->string('conversation_option_value')->nullable()->after('current_index');
        });
    }
};
