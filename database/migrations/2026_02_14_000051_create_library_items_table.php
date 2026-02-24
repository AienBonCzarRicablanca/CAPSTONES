<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('library_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('library_category_id')->nullable()->constrained('library_categories')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->string('title');
            $table->string('language', 20)->default('English'); // English, Tagalog
            $table->string('difficulty', 20)->default('Beginner'); // Beginner, Intermediate, Advanced
            $table->longText('text_content')->nullable();
            $table->string('audio_path')->nullable(); // TTS placeholder
            $table->timestamps();

            $table->index(['language', 'difficulty', 'library_category_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('library_items');
    }
};
