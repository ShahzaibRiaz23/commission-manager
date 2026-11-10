<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\DashboardController;


Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

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

// Update the existing dashboard route
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/auth.php';