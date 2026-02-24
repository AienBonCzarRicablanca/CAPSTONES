<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('class_student', function (Blueprint $table) {
            $table->string('status', 20)->default('approved')->after('joined_at');
            $table->timestamp('requested_at')->nullable()->after('status');
            $table->timestamp('decided_at')->nullable()->after('requested_at');
            $table->foreignId('decided_by')->nullable()->after('decided_at')->constrained('users')->nullOnDelete();

            $table->index(['class_id', 'status']);
            $table->index(['student_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('class_student', function (Blueprint $table) {
            $table->dropIndex(['class_id', 'status']);
            $table->dropIndex(['student_id', 'status']);
            $table->dropConstrainedForeignId('decided_by');
            $table->dropColumn(['status', 'requested_at', 'decided_at']);
        });
    }
};
