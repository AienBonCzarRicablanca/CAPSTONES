<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reading_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reading_passage_id')->constrained()->cascadeOnDelete();
            $table->string('audio_filename');
            $table->text('transcription')->nullable(); // What ML heard
            $table->decimal('accuracy_score', 5, 2)->nullable(); // 0-100%
            $table->integer('words_per_minute')->nullable();
            $table->decimal('fluency_score', 3, 2)->nullable(); // 0-5 scale
            $table->enum('grade', ['Excellent', 'Good', 'Fair', 'Needs Practice'])->nullable();
            $table->text('errors')->nullable(); // JSON array of errors
            $table->text('recommendations')->nullable(); // JSON array of tips
            $table->integer('duration_seconds')->nullable();
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->text('teacher_feedback')->nullable();
            $table->integer('teacher_score')->nullable(); // 0-100
            $table->timestamp('assessed_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reading_assessments');
    }
};
