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
        Schema::table('bars', function (Blueprint $table) {
            $table->boolean('offers_beer')->default(true)->after('is_demo');
            $table->boolean('offers_wine')->default(false)->after('offers_beer');
            $table->json('recommendation_questions_wine')->nullable()->after('recommendation_questions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bars', function (Blueprint $table) {
            $table->dropColumn(['offers_beer', 'offers_wine', 'recommendation_questions_wine']);
        });
    }
};

