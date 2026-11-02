<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks')->cascadeOnDelete();
            $table->foreignId('assignee_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('assigned_by')->constrained('users')->cascadeOnDelete();
            
            $table->enum('status', [
                'pending',
                'in_progress',
                'submitted',
                'completed',
                'returned',
                'cancelled'
            ])->default('pending');

            $table->dateTime('assigned_at');
            $table->dateTime('started_at')->nullable();
            $table->dateTime('submitted_at')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->dateTime('deadline')->nullable(); // can override task deadline
            $table->enum('priority', ['high', 'medium', 'low'])->nullable(); // can override
            
            $table->boolean('is_active')->default(true);
            $table->text('admin_notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();

            // Prevent duplicate active assignments
            $table->unique(['task_id', 'assignee_id', 'is_active'], 'unique_active_assignment');
            
            $table->index(['assignee_id', 'status']);
            $table->index(['task_id', 'is_active']);
            $table->index('deadline');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_assignments');
    }
};
