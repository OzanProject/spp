<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Backend\ClassRoomController;
use App\Http\Controllers\Backend\DashboardController as AdminDashboardController;
use App\Http\Controllers\Backend\InvoiceController;
use App\Http\Controllers\Backend\PaymentController;
use App\Http\Controllers\Backend\PdfController;
use App\Http\Controllers\Backend\ReportController;
use App\Http\Controllers\Backend\SettingController;
use App\Http\Controllers\Backend\StudentController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\Siswa\DashboardController as SiswaDashboardController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Forgot Password
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendOtp'])->name('password.email');
Route::get('/verify-otp', [AuthController::class, 'showVerifyOtp'])->name('password.verify.otp');
Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('password.verify.otp.submit');
Route::get('/reset-password', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

// Midtrans Webhook (Must be outside CSRF & Auth)
Route::post('/payments/midtrans-notification', [\App\Http\Controllers\Siswa\PaymentController::class, 'handleNotification'])->name('midtrans.notification');

Route::middleware('auth')->group(function () {
    // Admin routes
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'adminDashboard'])->name('admin.dashboard');
    Route::get('/admin/profile', [\App\Http\Controllers\Backend\ProfileController::class, 'edit'])->name('admin.profile.edit');
    Route::put('/admin/profile', [\App\Http\Controllers\Backend\ProfileController::class, 'update'])->name('admin.profile.update');
    Route::get('/admin/students/export', [StudentController::class, 'export'])->name('admin.students.export');
    Route::get('/admin/students/template', [StudentController::class, 'template'])->name('admin.students.template');
    Route::post('/admin/students/import', [StudentController::class, 'import'])->name('admin.students.import');
    Route::post('/admin/students/bulk-destroy', [StudentController::class, 'bulkDestroy'])->name('admin.students.bulkDestroy');
    Route::resource('/admin/students', StudentController::class)->names('admin.students');
    Route::resource('/admin/classes', ClassRoomController::class)->names('admin.classes');
    Route::resource('/admin/users', UserController::class)->names('admin.users');
    Route::post('/admin/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('admin.users.resetPassword');
    Route::post('/admin/invoices/bulk-destroy', [InvoiceController::class, 'bulkDestroy'])->name('admin.invoices.bulkDestroy');
    Route::resource('/admin/invoices', InvoiceController::class)->names('admin.invoices');
    Route::post('/admin/invoices/{invoice}/mark-paid', [InvoiceController::class, 'markPaid'])->name('admin.invoices.markPaid');
    Route::post('/admin/invoices/generate-bulk', [InvoiceController::class, 'generateBulk'])->name('admin.invoices.generateBulk');

    // Verifikasi Pembayaran
    Route::post('/admin/payments/bulk-destroy', [PaymentController::class, 'bulkDestroy'])->name('admin.payments.bulkDestroy');
    Route::get('/admin/payments', [PaymentController::class, 'index'])->name('admin.payments.index');
    Route::post('/admin/payments/{invoice}/confirm', [PaymentController::class, 'confirm'])->name('admin.payments.confirm');
    Route::post('/admin/payments/{invoice}/reject', [PaymentController::class, 'reject'])->name('admin.payments.reject');

    // Laporan
    Route::get('/admin/reports', [ReportController::class, 'index'])->name('admin.reports.index');

    // Pengaturan
    Route::get('/admin/settings', [SettingController::class, 'index'])->name('admin.settings.index');
    Route::put('/admin/settings', [SettingController::class, 'update'])->name('admin.settings.update');
    Route::get('/admin/settings/test-wa', [SettingController::class, 'testWa'])->name('admin.settings.testWa');
    Route::get('/admin/settings/test-midtrans', [SettingController::class, 'testMidtrans'])->name('admin.settings.testMidtrans');
    Route::get('/admin/settings/test-notification', [SettingController::class, 'testNotification'])->name('admin.settings.testNotification');
    Route::get('/admin/settings/test-wa-message', [SettingController::class, 'testWaMessage'])->name('admin.settings.testWaMessage');

    // PDF Export
    Route::get('/admin/pdf/invoice/{invoice}', [PdfController::class, 'invoice'])->name('admin.pdf.invoice');
    Route::get('/admin/pdf/report/{year}/{month}', [PdfController::class, 'report'])->name('admin.pdf.report');

    // Siswa routes
    Route::get('/siswa/dashboard', [SiswaDashboardController::class, 'siswaDashboard'])->name('siswa.dashboard');
    Route::get('/siswa/invoices', [\App\Http\Controllers\Siswa\InvoiceController::class, 'index'])->name('siswa.invoices.index');
    Route::get('/siswa/invoices/{invoice}', [\App\Http\Controllers\Siswa\InvoiceController::class, 'show'])->name('siswa.invoices.show');
    Route::get('/siswa/payments', [\App\Http\Controllers\Siswa\PaymentController::class, 'index'])->name('siswa.payments.index');
    Route::get('/siswa/payments/upload', [\App\Http\Controllers\Siswa\PaymentController::class, 'create'])->name('siswa.payments.create');
    Route::post('/siswa/payments/upload', [\App\Http\Controllers\Siswa\PaymentController::class, 'store'])->name('siswa.payments.store');
    Route::post('/siswa/payments/snap-token', [\App\Http\Controllers\Siswa\PaymentController::class, 'getSnapToken'])->name('siswa.payments.getSnapToken');
    Route::post('/siswa/payments/verify', [\App\Http\Controllers\Siswa\PaymentController::class, 'verify'])->name('siswa.payments.verify');
    Route::get('/siswa/profile', [\App\Http\Controllers\Siswa\ProfileController::class, 'edit'])->name('siswa.profile.edit');
    Route::put('/siswa/profile', [\App\Http\Controllers\Siswa\ProfileController::class, 'update'])->name('siswa.profile.update');
    Route::get('/siswa/timeline', [\App\Http\Controllers\Siswa\TimelineController::class, 'index'])->name('siswa.timeline');
    Route::get('/siswa/notifications', function() {
        return view('siswa.notifications');
    })->name('siswa.notifications');
    Route::post('/siswa/notifications/{id}/read', function($id) {
        Auth::user()->notifications()->findOrFail($id)->markAsRead();
        return back();
    })->name('siswa.notifications.read');
    Route::post('/siswa/notifications/read-all', function() {
        Auth::user()->unreadNotifications->markAsRead();
        return back();
    })->name('siswa.notifications.readAll');
    Route::delete('/siswa/notifications/{id}', function($id) {
        Auth::user()->notifications()->findOrFail($id)->delete();
        return back()->with('success', 'Notifikasi berhasil dihapus.');
    })->name('siswa.notifications.destroy');
    Route::delete('/siswa/notifications-clear', function() {
        Auth::user()->notifications()->delete();
        return back()->with('success', 'Semua notifikasi berhasil dihapus.');
    })->name('siswa.notifications.clear');
});
