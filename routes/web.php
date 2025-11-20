<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RequisitionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ReportController;

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Guest routes (public)
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register.form');
Route::post('/register', [RegisterController::class, 'register'])->name('register');

// Password Reset
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Requisition
    Route::get('/requisitions/create', [RequisitionController::class, 'create'])->name('requisitions.create');
    Route::post('/requisitions', [RequisitionController::class, 'store'])->name('requisitions.store');
    Route::get('/requisitions/{requisition}', [RequisitionController::class, 'show'])->name('requisitions.show');
    Route::get('/requisitions/{requisition}/receipt', [RequisitionController::class, 'downloadReceipt'])->name('requisitions.receipt');
    Route::get('/requisitions/{requisition}/edit', [RequisitionController::class, 'edit'])->name('requisitions.edit');
    Route::put('/requisitions/{requisition}', [RequisitionController::class, 'update'])->name('requisitions.update');
    Route::delete('/requisitions/{requisition}', [RequisitionController::class, 'destroy'])->name('requisitions.destroy');
    Route::post('/requisitions/{requisition}/confirm', [RequisitionController::class, 'confirmReceipt'])->name('requisitions.confirm');
    Route::post('/requisitions/{requisition}/update-status', [RequisitionController::class, 'updateStatus'])->name('requisitions.update-status');
    // JSON endpoint for a single requisition (used by AJAX updates)
    Route::get('/requisitions/{requisition}/json', [RequisitionController::class, 'json'])->name('requisitions.json');

    // Admin-only routes
    Route::middleware('role:admin')->group(function () {
        // Users management (Admin)
        Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users.index');
        Route::get('/admin/users/create', [UserController::class, 'create'])->name('admin.users.create');
        Route::post('/admin/users', [UserController::class, 'store'])->name('admin.users.store');
        Route::get('/admin/users/{user}', [UserController::class, 'show'])->name('admin.users.show');
        Route::get('/admin/users/{user}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
        Route::put('/admin/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
        Route::delete('/admin/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');

        // Reports
        Route::get('/admin/reports', [ReportController::class, 'index'])->name('admin.reports.index');
        Route::get('/admin/reports/export/pdf', [ReportController::class, 'exportPDF'])->name('admin.reports.export.pdf');
        Route::get('/admin/summary', [ReportController::class, 'summary'])->name('admin.reports.summary');
    });

    // Accountant routes
    Route::middleware('role:accountant,admin')->group(function () {
        Route::get('/accountant/dashboard', [RequisitionController::class, 'accountantDashboard'])->name('accountant.dashboard');
    });

    // Shared Reports (Admin + Accountant)
    Route::middleware('role:admin,accountant')->group(function () {
        Route::get('/reports/pdf', [ReportController::class, 'exportPDF'])->name('reports.pdf');
    });

    // Notifications
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread', [\App\Http\Controllers\NotificationController::class, 'unread'])->name('notifications.unread');
    Route::post('/notifications/{notification}/mark-as-read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/notifications/mark-all-as-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::delete('/notifications/{notification}', [\App\Http\Controllers\NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::delete('/notifications', [\App\Http\Controllers\NotificationController::class, 'destroyAll'])->name('notifications.destroyAll');

    // AJAX endpoints for navbar
    Route::get('/ajax/notifications/unread-count', [\App\Http\Controllers\NotificationController::class, 'getUnreadCount'])->name('notifications.unreadCount');
    Route::get('/ajax/notifications/latest', [\App\Http\Controllers\NotificationController::class, 'getLatest'])->name('notifications.getLatest');
});
