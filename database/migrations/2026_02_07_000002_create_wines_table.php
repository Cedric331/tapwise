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
        Schema::create('wines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bar_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('color');
            $table->string('grape')->nullable();
            $table->string('region')->nullable();
            $table->integer('abv_x10'); // ABV stored as integer (e.g., 125 for 12.5%)
            $table->text('description')->nullable();
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
        Schema::dropIfExists('wines');
    }
};

