<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\TaskCategory;
use App\Models\SystemSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'System Admin',
            'email' => 'admin@taskmaster.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        User::create([
            'name' => 'Team Supervisor',
            'email' => 'supervisor@taskmaster.test',
            'password' => Hash::make('password'),
            'role' => 'supervisor',
            'status' => 'active',
        ]);

        $assignees = [
            ['name' => 'Rahim Khan', 'email' => 'rahim@taskmaster.test'],
            ['name' => 'Karim Ahmed', 'email' => 'karim@taskmaster.test'],
            ['name' => 'Hasan Ali', 'email' => 'hasan@taskmaster.test'],
            ['name' => 'Nusrat Jahan', 'email' => 'nusrat@taskmaster.test'],
            ['name' => 'Sakib Rahman', 'email' => 'sakib@taskmaster.test'],
        ];

        foreach ($assignees as $data) {
            User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make('password'),
                'role' => 'assignee',
                'status' => 'active',
            ]);
        }

        $categories = [
            ['name' => 'Development', 'slug' => 'development', 'color' => '#2563EB'],
            ['name' => 'Design', 'slug' => 'design', 'color' => '#7C3AED'],
            ['name' => 'Marketing', 'slug' => 'marketing', 'color' => '#DB2777'],
            ['name' => 'Content', 'slug' => 'content', 'color' => '#059669'],
            ['name' => 'Support', 'slug' => 'support', 'color' => '#D97706'],
            ['name' => 'Research', 'slug' => 'research', 'color' => '#4B5563'],
        ];

        foreach ($categories as $cat) {
            TaskCategory::create($cat);
        }

        $settings = [
            ['key' => 'app_name', 'value' => 'TASK MASTER', 'type' => 'string', 'group' => 'general'],
            ['key' => 'default_priority', 'value' => 'medium', 'type' => 'string', 'group' => 'tasks'],
            ['key' => 'deadline_reminder_hours', 'value' => '24', 'type' => 'integer', 'group' => 'notifications'],
            ['key' => 'require_submission_approval', 'value' => '1', 'type' => 'boolean', 'group' => 'tasks'],
            ['key' => 'require_assistance_approval', 'value' => '0', 'type' => 'boolean', 'group' => 'tasks'],
            ['key' => 'allow_self_assigned_tasks', 'value' => '1', 'type' => 'boolean', 'group' => 'tasks'],
            ['key' => 'allow_recurring_tasks', 'value' => '1', 'type' => 'boolean', 'group' => 'tasks'],
            ['key' => 'allow_task_attachments', 'value' => '1', 'type' => 'boolean', 'group' => 'tasks'],
            ['key' => 'max_attachment_size_mb', 'value' => '10', 'type' => 'integer', 'group' => 'tasks'],
        ];

        foreach ($settings as $setting) {
            SystemSetting::create($setting);
        }

        $this->command->info('Database seeded successfully!');
        $this->command->info('Admin: admin@taskmaster.test / password');
        $this->command->info('Supervisor: supervisor@taskmaster.test / password');
        $this->command->info('Assignee: rahim@taskmaster.test / password');
    }
}
