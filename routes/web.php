<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SalesDashboardController;
use App\Http\Controllers\VADashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Client Management Routes
    Route::resource('clients', ClientController::class);
});

// Project Management Routes
Route::resource('projects', ProjectController::class);

// Payment & Commission Ledger Routes
Route::get('projects/{project}/payments/create', [PaymentController::class, 'create'])->name('payments.create');
Route::post('projects/{project}/payments', [PaymentController::class, 'store'])->name('payments.store');
Route::get('financial/payments', [PaymentController::class, 'index'])->name('payments.index');
Route::patch('financial/payments/{payment}/status', [PaymentController::class, 'updateStatus'])->name('payments.updateStatus');
Route::get('financial/payments/export', [PaymentController::class, 'export'])->name('payments.export');

// Role-Based Dashboards & Super Admin Management
Route::middleware(['auth', 'verified'])->group(function () {

    // Main / Admin Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // Admin: Create New Staff Account Form (VA / Sales)
    Route::get('/admin/users/create', [DashboardController::class, 'createUser'])
        ->middleware('role:Super Admin')
        ->name('admin.users.create');

    // Admin: Store New Staff Account in Database
    Route::post('/admin/users', [DashboardController::class, 'storeUser'])
        ->middleware('role:Super Admin')
        ->name('admin.users.store');

    // Virtual Assistant (VA) Dashboard
    Route::get('/va/dashboard', [VADashboardController::class, 'index'])
        ->middleware('role:VA')
        ->name('va.dashboard');

    // Sales Officer Dashboard
    Route::get('/sales/dashboard', [SalesDashboardController::class, 'index'])
        ->middleware('role:Sales Officer')
        ->name('sales.dashboard');

});

require __DIR__.'/auth.php';
