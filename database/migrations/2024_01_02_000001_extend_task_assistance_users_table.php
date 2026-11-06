<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('task_assistance_users', function (Blueprint $table) {
            $table->text('helper_note')->nullable()->after('status');
            $table->text('requester_note')->nullable()->after('helper_note');
            $table->timestamp('submitted_at')->nullable()->after('requester_note');
            $table->timestamp('responded_at')->nullable()->after('submitted_at');
        });

        // Expand status enum values
        DB::statement("ALTER TABLE task_assistance_users MODIFY COLUMN status ENUM('pending','accepted','declined','submitted','completed','rejected') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        Schema::table('task_assistance_users', function (Blueprint $table) {
            $table->dropColumn(['helper_note', 'requester_note', 'submitted_at', 'responded_at']);
        });
        DB::statement("ALTER TABLE task_assistance_users MODIFY COLUMN status ENUM('pending','accepted','declined') NOT NULL DEFAULT 'pending'");
    }
};
