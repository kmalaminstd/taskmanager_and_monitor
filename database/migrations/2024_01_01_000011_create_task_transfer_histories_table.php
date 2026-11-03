<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_transfer_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks')->cascadeOnDelete();
            $table->foreignId('old_assignment_id')->nullable()->constrained('task_assignments')->nullOnDelete();
            $table->foreignId('previous_assignee_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('new_assignee_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('transferred_by')->constrained('users')->cascadeOnDelete();
            $table->enum('transfer_type', ['admin_direct', 'request_approved']);
            $table->text('reason')->nullable();
            $table->text('notes')->nullable();
            $table->dateTime('transferred_at');
            $table->timestamps();

            $table->index('task_id');
            $table->index('transferred_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_transfer_histories');
    }
};
