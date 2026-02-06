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
        Schema::create('beers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bar_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('brewery')->nullable();
            $table->string('style')->nullable();
            $table->string('color');
            $table->integer('abv_x10'); // ABV stored as integer (e.g., 45 for 4.5%)
            $table->integer('ibu')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_on_tap')->default(false);
            $table->boolean('is_available')->default(true);
            $table->integer('price')->nullable(); // Price in cents
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beers');
    }
};
