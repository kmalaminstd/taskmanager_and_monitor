<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->nullable()->constrained('tasks')->cascadeOnDelete();
            $table->foreignId('task_assignment_id')->nullable()->constrained('task_assignments')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action'); // created, updated, assigned, transferred, status_changed, etc.
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['task_id', 'created_at']);
            $table->index(['task_assignment_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_histories');
    }
};
