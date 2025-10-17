<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RequisitionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ForgotPasswordController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    
    Route::get('/requisitions/create', [RequisitionController::class, 'create'])->name('requisitions.create');
    Route::post('/requisitions', [RequisitionController::class, 'store'])->name('requisitions.store');
    Route::get('/requisitions/{requisition}/edit', [RequisitionController::class, 'edit'])->name('requisitions.edit');
    Route::put('/requisitions/{requisition}', [RequisitionController::class, 'update'])->name('requisitions.update');
    Route::delete('/requisitions/{requisition}', [RequisitionController::class, 'destroy'])->name('requisitions.destroy');
    
    Route::post('/requisitions/{requisition}/confirm', [RequisitionController::class, 'confirmReceipt'])->name('requisitions.confirm');
    Route::post('/requisitions/{requisition}/update-status', [RequisitionController::class, 'updateStatus'])->name('requisitions.update-status');
    
    // ✅ Admin routes (now properly defined)
    Route::middleware('role:admin')->group(function () {
        Route::resource('admin/users', UserController::class)->except(['show']);
        Route::get('/admin/reports', [ReportController::class, 'index'])->name('admin.reports.index');
        Route::get('/admin/reports/export/pdf', [ReportController::class, 'exportPDF'])->name('admin.reports.export.pdf');
        Route::get('/admin/summary', [ReportController::class, 'summary'])->name('admin.reports.summary');
    });
});
Route::middleware(['auth', 'role:admin|accountant'])->group(function () {
    Route::get('/reports/pdf', [ReportController::class, 'exportPDF'])->name('reports.pdf');
});

// Accountant routes
Route::middleware('role:accountant,admin')->group(function () {
    Route::get('/accountant/dashboard', [RequisitionController::class, 'accountantDashboard'])->name('accountant.dashboard');
});
Route::middleware(['auth', 'role:admin|accountant'])->group(function () {
    Route::get('/reports/pdf', [ReportController::class, 'exportPDF'])->name('reports.pdf');
});

// Existing requisition routes (ensure update-status exists)
Route::post('/requisitions/{requisition}/update-status', [RequisitionController::class, 'updateStatus'])->name('requisitions.update-status');

Route::middleware(['auth', 'accountant'])->group(function () {
    Route::get('/accountant/dashboard', [RequisitionController::class, 'accountantDashboard'])->name('accountant.dashboard');
}); 

// Forgot Password Routes
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/verify-security-question/{email}', [ForgotPasswordController::class, 'showSecurityQuestionForm'])->name('password.verify.form');
Route::post('/verify-security-answer', [ForgotPasswordController::class, 'verifySecurityAnswer'])->name('password.verify');
Route::get('/reset-password/{token}/{email}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset.form');
Route::post('/reset-password', [ForgotPasswordController::class, 'reset'])->name('password.update');
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// routes/web.php
Route::middleware(['auth', 'role:admin|accountant'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/pdf', [ReportController::class, 'exportPDF'])->name('reports.pdf');
    Route::get('/reports/summary', [ReportController::class, 'summary'])->name('reports.summary');
});