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

Route::prefix('admin')->name('admin.')->middleware(['auth', 'is_admin'])->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
});

// Root redirect
Route::get('/', function () {
    return redirect()->route('login');
});

// Guest routes (no authentication required)
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// ✅ PASSWORD RESET ROUTES (Added for Forgot Password functionality)
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
    
    // Requisitions
    Route::get('/requisitions/create', [RequisitionController::class, 'create'])->name('requisitions.create');
    Route::post('/requisitions', [RequisitionController::class, 'store'])->name('requisitions.store');
    Route::get('/requisitions/{requisition}/edit', [RequisitionController::class, 'edit'])->name('requisitions.edit');
    Route::put('/requisitions/{requisition}', [RequisitionController::class, 'update'])->name('requisitions.update');
    Route::delete('/requisitions/{requisition}', [RequisitionController::class, 'destroy'])->name('requisitions.destroy');
    
    Route::post('/requisitions/{requisition}/confirm', [RequisitionController::class, 'confirmReceipt'])->name('requisitions.confirm');
    Route::post('/requisitions/{requisition}/update-status', [RequisitionController::class, 'updateStatus'])->name('requisitions.update-status');
    
    // Admin routes (admin only)
    Route::middleware('role:admin')->group(function () {
        Route::resource('admin/users', UserController::class)->except(['show']);
        Route::get('/admin/reports', [ReportController::class, 'index'])->name('admin.reports.index');
        Route::get('/admin/reports/export/pdf', [ReportController::class, 'exportPDF'])->name('admin.reports.export.pdf');
        Route::get('/admin/summary', [ReportController::class, 'summary'])->name('admin.reports.summary');
    });
    
    
   Route::middleware('role:accountant,admin')->group(function () {
        Route::get('/accountant/dashboard', [RequisitionController::class, 'accountantDashboard'])->name('accountant.dashboard');
    });
    
   // Shared reports
    Route::middleware('role:admin,accountant')->group(function () {
        Route::get('/reports/pdf', [ReportController::class, 'exportPDF'])->name('reports.pdf');
    });
});
// Custom Security Question Password Reset Routes
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/verify-security-question/{email}', [ForgotPasswordController::class, 'showSecurityQuestionForm'])->name('password.verify.form');
Route::post('/verify-security-answer', [ForgotPasswordController::class, 'verifySecurityAnswer'])->name('password.verify');
Route::get('/reset-password/{token}/{email}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset.form');
Route::post('/reset-password', [ForgotPasswordController::class, 'reset'])->name('password.update');
