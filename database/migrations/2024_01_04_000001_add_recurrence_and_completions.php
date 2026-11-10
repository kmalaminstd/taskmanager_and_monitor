<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('personal_tasks', function (Blueprint $table) {
            $table->unsignedTinyInteger('recurrence_weekday')->nullable()->after('frequency'); // 0=Sun .. 6=Sat
            $table->unsignedTinyInteger('recurrence_monthday')->nullable()->after('recurrence_weekday'); // 1-31
            $table->unsignedSmallInteger('reminder_days_before')->default(1)->after('recurrence_monthday');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->unsignedTinyInteger('recurrence_weekday')->nullable()->after('frequency');
            $table->unsignedTinyInteger('recurrence_monthday')->nullable()->after('recurrence_weekday');
            $table->unsignedSmallInteger('reminder_days_before')->default(1)->after('recurrence_monthday');
        });

        // Track completion of recurring periods (daily/weekly/monthly)
        Schema::create('recurring_completions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('taskable_type'); // App\Models\PersonalTask or TaskAssignment
            $table->unsignedBigInteger('taskable_id');
            $table->string('period_key'); // e.g. 2026-11-10, 2026-W45, 2026-11
            $table->string('frequency'); // daily, weekly, monthly
            $table->timestamp('completed_at');
            $table->timestamps();

            $table->unique(['user_id', 'taskable_type', 'taskable_id', 'period_key'], 'recurring_completions_unique');
            $table->index(['user_id', 'frequency']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recurring_completions');
        Schema::table('personal_tasks', function (Blueprint $table) {
            $table->dropColumn(['recurrence_weekday', 'recurrence_monthday', 'reminder_days_before']);
        });
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['recurrence_weekday', 'recurrence_monthday', 'reminder_days_before']);
        });
    }
};
