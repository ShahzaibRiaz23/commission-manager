<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SalesDashboardController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\VADashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // User Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ------------------------------------------------------------------------
    // Management Routes (Only Super Admin & Sales Officer)
    // NOTE: Static routes like '/create' MUST be placed BEFORE wildcard routes like '/{client}'
    // ------------------------------------------------------------------------
    Route::middleware('role:Super Admin|Sales Officer')->group(function () {
        // Client Create / Store / Edit / Update / Destroy
        Route::get('clients/create', [ClientController::class, 'create'])->name('clients.create');
        Route::post('clients', [ClientController::class, 'store'])->name('clients.store');
        Route::get('clients/{client}/edit', [ClientController::class, 'edit'])->name('clients.edit');
        Route::put('clients/{client}', [ClientController::class, 'update'])->name('clients.update');
        Route::delete('clients/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');

        // Project Create / Store / Edit / Update / Destroy
        Route::get('projects/create', [ProjectController::class, 'create'])->name('projects.create');
        Route::post('projects', [ProjectController::class, 'store'])->name('projects.store');
        Route::get('projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
        Route::put('projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
        Route::delete('projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');

        // Record Payments
        Route::get('projects/{project}/payments/create', [PaymentController::class, 'create'])->name('payments.create');
        Route::post('projects/{project}/payments', [PaymentController::class, 'store'])->name('payments.store');
    });

    // Super Admin Only: Status Approval, User Management & Commission Settings
    Route::middleware('role:Super Admin')->group(function () {
        Route::patch('financial/payments/{payment}/status', [PaymentController::class, 'updateStatus'])->name('payments.updateStatus');
        
        Route::get('/admin/users/create', [DashboardController::class, 'createUser'])->name('admin.users.create');
        Route::post('/admin/users', [DashboardController::class, 'storeUser'])->name('admin.users.store');

        // Commission Settings Routes
        Route::get('/admin/settings', [SettingController::class, 'index'])->name('admin.settings.index');
        Route::put('/admin/settings', [SettingController::class, 'update'])->name('admin.settings.update');
    });

    // ------------------------------------------------------------------------
    // Read-Only & Wildcard Routes (Index and Show pages)
    // ------------------------------------------------------------------------
    Route::get('clients', [ClientController::class, 'index'])->name('clients.index');
    Route::get('clients/{client}', [ClientController::class, 'show'])->name('clients.show');

    Route::get('projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('projects/{project}', [ProjectController::class, 'show'])->name('projects.show');

    Route::get('financial/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('financial/payments/export', [PaymentController::class, 'export'])->name('payments.export');

    // ------------------------------------------------------------------------
    // Dashboards
    // ------------------------------------------------------------------------
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/va/dashboard', [VADashboardController::class, 'index'])
        ->middleware('role:VA')
        ->name('va.dashboard');

    Route::get('/sales/dashboard', [SalesDashboardController::class, 'index'])
        ->middleware('role:Sales Officer')
        ->name('sales.dashboard');
});

require __DIR__.'/auth.php';