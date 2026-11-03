<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('task_categories')->nullOnDelete();
            $table->enum('priority', ['high', 'medium', 'low'])->default('medium');
            $table->dateTime('deadline')->nullable();
            $table->enum('task_type', ['one_time', 'recurring'])->default('one_time');
            $table->string('frequency')->nullable(); // daily, weekly, monthly for recurring
            $table->text('instructions')->nullable();
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['created_by', 'status']);
            $table->index('deadline');
            $table->index('priority');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
