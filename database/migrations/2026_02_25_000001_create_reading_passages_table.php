<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reading_passages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->enum('language', ['English', 'Tagalog']);
            $table->enum('difficulty', ['Beginner', 'Intermediate', 'Advanced']);
            $table->foreignId('category_id')->nullable()->constrained('library_categories')->nullOnDelete();
            $table->integer('word_count');
            $table->integer('expected_wpm')->default(60); // Expected reading speed
            $table->integer('time_limit_seconds')->nullable(); // Optional time limit
            $table->text('comprehension_questions')->nullable(); // JSON array of questions
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reading_passages');
    }
};
