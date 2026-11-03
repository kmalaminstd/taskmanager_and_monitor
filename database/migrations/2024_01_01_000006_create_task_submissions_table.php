<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_assignment_id')->constrained('task_assignments')->cascadeOnDelete();
            $table->foreignId('submitted_by')->constrained('users')->cascadeOnDelete();
            $table->text('completion_details')->nullable();
            $table->text('completion_note')->nullable();
            $table->enum('status', ['pending', 'approved', 'returned'])->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('reviewed_at')->nullable();
            $table->text('return_note')->nullable();
            $table->timestamps();

            $table->index(['task_assignment_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_submissions');
    }
};
