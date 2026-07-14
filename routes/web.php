<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Classes Route
    Route::middleware('permission:manage_classes')->group(function () {
        Route::post('classes/import', [\App\Http\Controllers\SchoolClassController::class, 'import'])->name('classes.import');
        Route::get('classes/export', [\App\Http\Controllers\SchoolClassController::class, 'export'])->name('classes.export');
        Route::resource('classes', \App\Http\Controllers\SchoolClassController::class);
    });
    
    // Students Route
    Route::middleware('permission:manage_students')->group(function () {
        Route::post('students/import', [\App\Http\Controllers\StudentController::class, 'import'])->name('students.import');
        Route::get('students/export', [\App\Http\Controllers\StudentController::class, 'export'])->name('students.export');
        Route::delete('students/bulk-destroy', [\App\Http\Controllers\StudentController::class, 'bulkDestroy'])->name('students.bulk-destroy');
        Route::resource('students', \App\Http\Controllers\StudentController::class);
        
        // Alumni routes
        Route::get('alumni', [\App\Http\Controllers\AlumniController::class, 'index'])->name('alumni.index');
        Route::get('alumni/{alumnus}', [\App\Http\Controllers\AlumniController::class, 'show'])->name('alumni.show');
        Route::get('alumni/{alumnus}/edit', [\App\Http\Controllers\AlumniController::class, 'edit'])->name('alumni.edit');
        Route::put('alumni/{alumnus}', [\App\Http\Controllers\AlumniController::class, 'update'])->name('alumni.update');
        Route::delete('alumni/bulk-destroy', [\App\Http\Controllers\AlumniController::class, 'bulkDestroy'])->name('alumni.bulk-destroy');
        Route::delete('alumni/{alumnus}', [\App\Http\Controllers\AlumniController::class, 'destroy'])->name('alumni.destroy');
        Route::post('alumni/{alumnus}/send-wa', [\App\Http\Controllers\AlumniController::class, 'sendWa'])->name('alumni.send-wa');
    });

    // Data Master Export Import (Combine)
    Route::middleware('permission:manage_students')->group(function () {
        Route::get('data-master', [\App\Http\Controllers\DataMasterController::class, 'index'])->name('data-master.index');
    });

    // Kenaikan Kelas / Mutasi
    Route::middleware('permission:manage_promotions')->group(function () {
        Route::get('promotions', [\App\Http\Controllers\PromotionController::class, 'index'])->name('promotions.index');
        Route::post('promotions', [\App\Http\Controllers\PromotionController::class, 'process'])->name('promotions.process');
    });
    
    // Finance Posts Route
    Route::middleware('permission:manage_finance_posts')->group(function () {
        Route::resource('finance-posts', \App\Http\Controllers\FinancePostController::class)->except(['show']);
    });
    
    // Bills Route
    Route::middleware('permission:manage_bills')->group(function () {
        Route::get('bills/generate', [\App\Http\Controllers\BillController::class, 'create'])->name('bills.generate');
        Route::post('bills/generate', [\App\Http\Controllers\BillController::class, 'store'])->name('bills.store');
        Route::get('bills', [\App\Http\Controllers\BillController::class, 'index'])->name('bills.index');
        Route::get('api/finance-posts/{financePost}/amount', [\App\Http\Controllers\BillController::class, 'getFinancePostAmount']);
    });

    // Payments Route
    Route::middleware('permission:manage_payments')->group(function () {
        Route::get('payments', [\App\Http\Controllers\PaymentController::class, 'index'])->name('payments.index');
        Route::post('payments', [\App\Http\Controllers\PaymentController::class, 'store'])->name('payments.store');
        Route::get('payments/{payment}/print', [\App\Http\Controllers\PaymentController::class, 'print'])->name('payments.print');
    });

    // Expenses Route
    Route::middleware('permission:manage_expenses')->group(function () {
        Route::resource('expense-categories', \App\Http\Controllers\ExpenseCategoryController::class)->except(['show']);
        Route::resource('expenses', \App\Http\Controllers\ExpenseController::class)->except(['edit', 'update', 'destroy']);
        Route::post('expenses/{expense}/approve', [\App\Http\Controllers\ExpenseController::class, 'approve'])->name('expenses.approve');
        Route::post('expenses/{expense}/reject', [\App\Http\Controllers\ExpenseController::class, 'reject'])->name('expenses.reject');
    });

    // Reports Route
    Route::middleware('permission:view_reports')->group(function () {
        Route::get('reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/cash', [\App\Http\Controllers\ReportController::class, 'cash'])->name('reports.cash');
        Route::get('reports/payment', [\App\Http\Controllers\ReportController::class, 'payment'])->name('reports.payment');
        Route::get('reports/expense', [\App\Http\Controllers\ReportController::class, 'expense'])->name('reports.expense');
        Route::get('reports/arrear', [\App\Http\Controllers\ReportController::class, 'arrear'])->name('reports.arrear');
    });

    // Audit Trail Route
    Route::middleware('permission:view_audit_trails')->group(function () {
        Route::get('audit-trails', [\App\Http\Controllers\AuditTrailController::class, 'index'])->name('audit-trails.index');
        Route::get('audit-trails/{auditTrail}', [\App\Http\Controllers\AuditTrailController::class, 'show'])->name('audit-trails.show');
    });

    // WA Gateway Route (Accessible by all authenticated roles)
    Route::get('wa-gateway', [\App\Http\Controllers\WaGatewayController::class, 'index'])->name('wa-gateway.index');
    Route::get('wa-gateway/status', [\App\Http\Controllers\WaGatewayController::class, 'status'])->name('wa-gateway.status');
    Route::post('wa-gateway/send-bulk', [\App\Http\Controllers\WaGatewayController::class, 'sendBulk'])->name('wa-gateway.send-bulk');

    // Settings, Roles, & Users Route
    Route::middleware('permission:manage_settings')->group(function () {
        Route::get('settings', [\App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
        Route::post('settings', [\App\Http\Controllers\SettingController::class, 'update'])->name('settings.update');
        Route::post('settings/reset', [\App\Http\Controllers\SettingController::class, 'resetData'])->name('settings.reset');
        Route::get('backup/manual', [\App\Http\Controllers\BackupController::class, 'manualBackup'])->name('backup.manual');
        Route::get('backup/download', [\App\Http\Controllers\BackupController::class, 'downloadBackup'])->name('backup.download');
        Route::post('backup/restore', [\App\Http\Controllers\BackupController::class, 'restoreBackup'])->name('backup.restore');
    });

    Route::middleware('permission:manage_roles')->group(function () {
        Route::resource('roles', \App\Http\Controllers\RoleController::class)->except(['show']);
    });

    Route::middleware('permission:manage_users')->group(function () {
        Route::resource('users', \App\Http\Controllers\UserController::class)->except(['show']);
    });

    Route::middleware('permission:manage_academic_years')->group(function () {
        Route::patch('academic-years/{academic_year}/set-active', [\App\Http\Controllers\AcademicYearController::class, 'setActive'])->name('academic-years.set-active');
        Route::resource('academic-years', \App\Http\Controllers\AcademicYearController::class)->except(['show']);
        Route::post('set-academic-year', [\App\Http\Controllers\AcademicYearSessionController::class, 'setSession'])->name('academic-years.set-session');
    });
});

require __DIR__.'/auth.php';
