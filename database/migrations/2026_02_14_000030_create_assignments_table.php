<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('type')->default('HOMEWORK'); // HOMEWORK, ACTIVITY
            $table->string('title');
            $table->longText('description')->nullable();
            $table->timestamp('due_at')->nullable();
            $table->timestamps();

            $table->index(['class_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
