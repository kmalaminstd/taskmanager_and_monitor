<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_subtasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks')->cascadeOnDelete();
            $table->foreignId('assignee_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->date('deadline')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['task_id', 'status']);
        });

        Schema::table('task_notes', function (Blueprint $table) {
            $table->foreignId('parent_id')->nullable()->after('user_id')->constrained('task_notes')->cascadeOnDelete();
            $table->boolean('is_pinned')->default(false)->after('note');
            $table->timestamp('edited_at')->nullable()->after('is_pinned');
        });

        Schema::create('task_note_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_note_id')->constrained('task_notes')->cascadeOnDelete();
            $table->string('file_path');
            $table->string('original_name');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_note_attachments');
        Schema::table('task_notes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('parent_id');
            $table->dropColumn(['is_pinned', 'edited_at']);
        });
        Schema::dropIfExists('task_subtasks');
    }
};
