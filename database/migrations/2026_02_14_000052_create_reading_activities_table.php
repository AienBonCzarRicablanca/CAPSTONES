<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reading_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('library_item_id')->constrained('library_items')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->string('type')->default('MCQ'); // MCQ, MATCHING, SHORT
            $table->json('answers')->nullable();
            $table->unsignedInteger('score')->nullable();
            $table->unsignedInteger('max_score')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['library_item_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reading_activities');
    }
};
