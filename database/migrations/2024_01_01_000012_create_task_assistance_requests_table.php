<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_assistance_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks')->cascadeOnDelete();
            $table->foreignId('task_assignment_id')->nullable()->constrained('task_assignments')->nullOnDelete();
            $table->foreignId('requester_id')->constrained('users')->cascadeOnDelete();
            $table->text('description');
            $table->enum('status', ['pending', 'accepted', 'rejected', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();

            $table->index(['task_id', 'status']);
            $table->index('requester_id');
        });

        Schema::create('task_assistance_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assistance_request_id')->constrained('task_assistance_requests')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['pending', 'accepted', 'declined'])->default('pending');
            $table->timestamps();

            $table->unique(['assistance_request_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_assistance_users');
        Schema::dropIfExists('task_assistance_requests');
    }
};
