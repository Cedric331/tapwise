<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('cover_image_path')->nullable();
            $table->longText('content');
            $table->json('tags')->nullable();
            $table->date('published_at')->nullable();
            $table->boolean('is_live')->default(false);
            $table->timestamps();

            $table->index(['is_live', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_posts');
    }
};

