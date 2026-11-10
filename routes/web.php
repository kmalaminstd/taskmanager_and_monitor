<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\TaskController as AdminTaskController;
use App\Http\Controllers\Admin\TransferController as AdminTransferController;
use App\Http\Controllers\Admin\ApprovalController as AdminApprovalController;
use App\Http\Controllers\Assignee\DashboardController as AssigneeDashboardController;
use App\Http\Controllers\Assignee\TaskController as AssigneeTaskController;
use App\Http\Controllers\Assignee\RequestController as AssigneeRequestController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        if ($user->isSupervisor()) {
            return redirect()->route('supervisor.dashboard');
        }
        return redirect()->route('assignee.dashboard');
    }
    return redirect()->route('login');
});

// Authentication
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// ==================== ADMIN ====================
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'active', 'admin'])
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Tasks
        Route::resource('tasks', AdminTaskController::class);

        // Assignments
        Route::get('/assignments', [App\Http\Controllers\Admin\AssignmentController::class, 'index'])->name('assignments.index');
        Route::get('/assignments/{assignment}', [App\Http\Controllers\Admin\AssignmentController::class, 'show'])->name('assignments.show');

        // Transfers
        Route::get('/transfers', [App\Http\Controllers\Admin\TransferHistoryController::class, 'index'])->name('transfers.index');
        Route::get('tasks/{task}/transfer', [AdminTransferController::class, 'create'])->name('transfers.create');
        Route::post('tasks/{task}/transfer', [AdminTransferController::class, 'store'])->name('transfers.store');

        // Approvals
        Route::get('/approvals', [AdminApprovalController::class, 'index'])->name('approvals.index');
        Route::post('/approvals/submissions/{submission}/approve', [AdminApprovalController::class, 'approveSubmission'])->name('approvals.submissions.approve');
        Route::post('/approvals/submissions/{submission}/return', [AdminApprovalController::class, 'returnSubmission'])->name('approvals.submissions.return');
        Route::post('/approvals/extensions/{extension}/approve', [AdminApprovalController::class, 'approveExtension'])->name('approvals.extensions.approve');
        Route::post('/approvals/extensions/{extension}/reject', [AdminApprovalController::class, 'rejectExtension'])->name('approvals.extensions.reject');
        Route::post('/approvals/transfers/{transfer}/approve', [AdminApprovalController::class, 'approveTransfer'])->name('approvals.transfers.approve');
        Route::post('/approvals/transfers/{transfer}/reject', [AdminApprovalController::class, 'rejectTransfer'])->name('approvals.transfers.reject');

        // Performance
        Route::get('/workload', [App\Http\Controllers\Admin\WorkloadController::class, 'index'])->name('workload.index');
        Route::get('/workload/{user}', [App\Http\Controllers\Admin\WorkloadController::class, 'show'])->name('workload.show');

        Route::get('/performance', [App\Http\Controllers\Admin\PerformanceController::class, 'index'])->name('performance.index');
        Route::get('/performance/{user}', [App\Http\Controllers\Admin\PerformanceController::class, 'show'])->name('performance.show');

        // Users
        Route::resource('users', App\Http\Controllers\Admin\UserController::class);
        Route::post('/users/{user}/toggle-status', [App\Http\Controllers\Admin\UserController::class, 'toggleStatus'])->name('users.toggle-status');

        Route::get('/assistance', [App\Http\Controllers\Admin\AssistanceController::class, 'index'])->name('assistance.index');
        Route::get('/assistance/{assistance}', [App\Http\Controllers\Admin\AssistanceController::class, 'show'])->name('assistance.show');
        Route::post('/tasks/{task}/assignments/{assignment}/assistance', [App\Http\Controllers\Admin\AssistanceController::class, 'storeForAssignment'])->name('assistance.store-for-assignment');
        Route::delete('/assistance/{assistance}', [App\Http\Controllers\Admin\AssistanceController::class, 'destroy'])->name('assistance.destroy');

        // Reports
        Route::get('/reports', [App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/generate', [App\Http\Controllers\Admin\ReportController::class, 'generate'])->name('reports.generate');

        // Settings
        Route::get('/settings', [App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');
    });

// ==================== ASSIGNEE ====================
Route::prefix('assignee')
    ->name('assignee.')
    ->middleware(['auth', 'active', 'assignee'])
    ->group(function () {
        Route::get('/dashboard', [AssigneeDashboardController::class, 'index'])->name('dashboard');

        // Tasks
        Route::get('/tasks', [AssigneeTaskController::class, 'index'])->name('tasks.index');
        Route::get('/tasks/{assignment}', [AssigneeTaskController::class, 'show'])->name('tasks.show');
        Route::post('/tasks/{assignment}/start', [AssigneeTaskController::class, 'start'])->name('tasks.start');
        Route::post('/tasks/{assignment}/submit', [AssigneeTaskController::class, 'submit'])->name('tasks.submit');

        // Requests
        Route::post('/tasks/{assignment}/request-extension', [AssigneeRequestController::class, 'requestExtension'])->name('tasks.request-extension');
        Route::post('/tasks/{assignment}/request-transfer', [AssigneeRequestController::class, 'requestTransfer'])->name('tasks.request-transfer');
        Route::post('/tasks/{assignment}/request-assistance', [AssigneeRequestController::class, 'requestAssistance'])->name('tasks.request-assistance');

        Route::get('/assistance', [App\Http\Controllers\Assignee\AssistanceController::class, 'index'])->name('assistance.index');
        Route::get('/assistance/{assistance}', [App\Http\Controllers\Assignee\AssistanceController::class, 'show'])->name('assistance.show');
        Route::post('/assistance/{assistance}/respond', [App\Http\Controllers\Assignee\AssistanceController::class, 'respond'])->name('assistance.respond');
        Route::post('/assistance/{assistance}/submit-work', [App\Http\Controllers\Assignee\AssistanceController::class, 'submitWork'])->name('assistance.submit-work');
        Route::post('/assistance/{assistance}/helpers/{userId}/approve', [App\Http\Controllers\Assignee\AssistanceController::class, 'approveHelper'])->name('assistance.approve-helper');
        Route::post('/assistance/{assistance}/helpers/{userId}/reject', [App\Http\Controllers\Assignee\AssistanceController::class, 'rejectHelper'])->name('assistance.reject-helper');
        Route::post('/assistance/{assistance}/helpers/{userId}/remove', [App\Http\Controllers\Assignee\AssistanceController::class, 'removeHelper'])->name('assistance.remove-helper');
        Route::post('/assistance/{assistance}/cancel', [App\Http\Controllers\Assignee\AssistanceController::class, 'cancel'])->name('assistance.cancel');
        Route::post('/assistance/{assistance}/complete', [App\Http\Controllers\Assignee\AssistanceController::class, 'complete'])->name('assistance.complete');

        // Personal Tasks
        Route::get('/personal-tasks', [App\Http\Controllers\Assignee\PersonalTaskController::class, 'index'])->name('personal-tasks.index');
        Route::get('/personal-tasks/create', [App\Http\Controllers\Assignee\PersonalTaskController::class, 'create'])->name('personal-tasks.create');
        Route::post('/personal-tasks', [App\Http\Controllers\Assignee\PersonalTaskController::class, 'store'])->name('personal-tasks.store');
        Route::get('/personal-tasks/{personalTask}/edit', [App\Http\Controllers\Assignee\PersonalTaskController::class, 'edit'])->name('personal-tasks.edit');
        Route::put('/personal-tasks/{personalTask}', [App\Http\Controllers\Assignee\PersonalTaskController::class, 'update'])->name('personal-tasks.update');
        Route::delete('/personal-tasks/{personalTask}', [App\Http\Controllers\Assignee\PersonalTaskController::class, 'destroy'])->name('personal-tasks.destroy');
        Route::post('/personal-tasks/{personalTask}/complete', [App\Http\Controllers\Assignee\PersonalTaskController::class, 'markComplete'])->name('personal-tasks.complete');

        // Performance
        Route::get('/performance', [App\Http\Controllers\Assignee\PerformanceController::class, 'index'])->name('performance.index');
        Route::post('/reminders/complete', [App\Http\Controllers\Assignee\ReminderController::class, 'complete'])->name('reminders.complete');
    });


// ==================== SUPERVISOR ====================
Route::prefix('supervisor')
    ->name('supervisor.')
    ->middleware(['auth', 'active', 'supervisor'])
    ->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Supervisor\DashboardController::class, 'index'])->name('dashboard');

        Route::get('/tasks', [App\Http\Controllers\Supervisor\TaskController::class, 'index'])->name('tasks.index');
        Route::get('/tasks/create', [App\Http\Controllers\Supervisor\TaskController::class, 'create'])->name('tasks.create');
        Route::post('/tasks', [App\Http\Controllers\Supervisor\TaskController::class, 'store'])->name('tasks.store');
        Route::get('/tasks/{task}', [App\Http\Controllers\Supervisor\TaskController::class, 'show'])->name('tasks.show');
        Route::get('/tasks/{task}/edit', [App\Http\Controllers\Supervisor\TaskController::class, 'edit'])->name('tasks.edit');
        Route::put('/tasks/{task}', [App\Http\Controllers\Supervisor\TaskController::class, 'update'])->name('tasks.update');

        Route::get('/approvals', [App\Http\Controllers\Supervisor\ApprovalController::class, 'index'])->name('approvals.index');
        Route::post('/approvals/submissions/{submission}/approve', [App\Http\Controllers\Supervisor\ApprovalController::class, 'approveSubmission'])->name('approvals.submissions.approve');
        Route::post('/approvals/submissions/{submission}/return', [App\Http\Controllers\Supervisor\ApprovalController::class, 'returnSubmission'])->name('approvals.submissions.return');
        Route::post('/approvals/extensions/{extension}/approve', [App\Http\Controllers\Supervisor\ApprovalController::class, 'approveExtension'])->name('approvals.extensions.approve');
        Route::post('/approvals/extensions/{extension}/reject', [App\Http\Controllers\Supervisor\ApprovalController::class, 'rejectExtension'])->name('approvals.extensions.reject');
        Route::post('/approvals/transfers/{transfer}/approve', [App\Http\Controllers\Supervisor\ApprovalController::class, 'approveTransfer'])->name('approvals.transfers.approve');
        Route::post('/approvals/transfers/{transfer}/reject', [App\Http\Controllers\Supervisor\ApprovalController::class, 'rejectTransfer'])->name('approvals.transfers.reject');

        Route::get('/team', [App\Http\Controllers\Supervisor\TeamController::class, 'index'])->name('team.index');
        Route::get('/team/{user}', [App\Http\Controllers\Supervisor\TeamController::class, 'show'])->name('team.show');

        Route::get('/assistance', [App\Http\Controllers\Supervisor\AssistanceController::class, 'index'])->name('assistance.index');
        Route::get('/assistance/{assistance}', [App\Http\Controllers\Supervisor\AssistanceController::class, 'show'])->name('assistance.show');

        Route::get('/performance', [App\Http\Controllers\Supervisor\PerformanceController::class, 'index'])->name('performance.index');
        Route::get('/performance/{user}', [App\Http\Controllers\Supervisor\PerformanceController::class, 'show'])->name('performance.show');
    });


// Task comments & subtasks (auth users)
Route::middleware(['auth', 'active'])->group(function () {
    Route::post('/tasks/{task}/comments', [App\Http\Controllers\TaskCommentController::class, 'store'])->name('tasks.comments.store');
    Route::put('/comments/{note}', [App\Http\Controllers\TaskCommentController::class, 'update'])->name('tasks.comments.update');
    Route::delete('/comments/{note}', [App\Http\Controllers\TaskCommentController::class, 'destroy'])->name('tasks.comments.destroy');
    Route::post('/comments/{note}/pin', [App\Http\Controllers\TaskCommentController::class, 'pin'])->name('tasks.comments.pin');

    Route::post('/tasks/{task}/subtasks', [App\Http\Controllers\TaskSubtaskController::class, 'store'])->name('tasks.subtasks.store');
    Route::put('/subtasks/{subtask}', [App\Http\Controllers\TaskSubtaskController::class, 'update'])->name('tasks.subtasks.update');
    Route::post('/subtasks/{subtask}/toggle', [App\Http\Controllers\TaskSubtaskController::class, 'toggleComplete'])->name('tasks.subtasks.toggle');
    Route::delete('/subtasks/{subtask}', [App\Http\Controllers\TaskSubtaskController::class, 'destroy'])->name('tasks.subtasks.destroy');
});

// Notifications (both roles)
Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
});


// Calendar (both roles)
Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/calendar', [App\Http\Controllers\CalendarController::class, 'index'])->name('calendar.index');
});
