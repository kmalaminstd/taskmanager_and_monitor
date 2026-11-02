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
        return auth()->user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('assignee.dashboard');
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

        // Transfers
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
        Route::get('/performance', [App\Http\Controllers\Admin\PerformanceController::class, 'index'])->name('performance.index');
        Route::get('/performance/{user}', [App\Http\Controllers\Admin\PerformanceController::class, 'show'])->name('performance.show');

        // Users
        Route::resource('users', App\Http\Controllers\Admin\UserController::class)->except(['show']);
        Route::post('/users/{user}/toggle-status', [App\Http\Controllers\Admin\UserController::class, 'toggleStatus'])->name('users.toggle-status');

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
