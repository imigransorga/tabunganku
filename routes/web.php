<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SavingsDepositController;
use App\Http\Controllers\SavingsGoalController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('accounts', AccountController::class)->except('show');
    Route::resource('categories', CategoryController::class)->only(['index', 'store', 'update', 'destroy']);

    Route::get('budgets', [BudgetController::class, 'index'])->name('budgets.index');
    Route::post('budgets', [BudgetController::class, 'store'])->name('budgets.store');
    Route::delete('budgets/{budget}', [BudgetController::class, 'destroy'])->name('budgets.destroy');

    Route::resource('transactions', TransactionController::class)->except('show');
    Route::patch('transactions/{transaction}/approve', [TransactionController::class, 'approve'])->name('transactions.approve');
    Route::patch('transactions/{transaction}/reject', [TransactionController::class, 'reject'])->name('transactions.reject');

    // parameter di-rename jadi {saving} agar pendek & konsisten.
    Route::resource('savings', SavingsGoalController::class)->parameters(['savings' => 'saving']);
    Route::post('savings/{saving}/deposits', [SavingsDepositController::class, 'store'])->name('savings.deposits.store');
    Route::delete('savings/{saving}/deposits/{deposit}', [SavingsDepositController::class, 'destroy'])->name('savings.deposits.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
