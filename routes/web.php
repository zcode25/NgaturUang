<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SignupController;
use App\Http\Controllers\SigninController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\StatementController;

// Route::get('/home', function () {
//     return view('home.index');
// })->name('home')->middleware('auth');

Route::controller(HomeController::class)->group(function () {
    Route::get('/home', 'index')->name('home')->middleware('auth');
});

Route::controller(SigninController::class)->group(function () {
    Route::get('/', 'index')->name('signin')->middleware('guest');
    Route::post('/signin/authenticate', 'authenticate')->name('signin.authenticate')->middleware('guest');
    Route::post('/signout', 'signout')->name('signout')->middleware('auth');
});


Route::controller(SignupController::class)->group(function () {
    Route::get('/signup', 'index')->name('signup')->middleware('guest');
    Route::post('/signup', 'store')->name('signup.store')->middleware('guest');
});

Route::controller(WalletController::class)->group(function () {
    Route::get('/wallet', 'index')->name('wallet')->middleware('auth');
    Route::get('/wallet/create', 'create')->name('wallet.create')->middleware('auth');
    Route::post('/wallet/store', 'store')->name('wallet.store')->middleware('auth');
    Route::get('/wallet/{id}/detail', 'detail')->name('wallet.detail')->middleware('auth');
    Route::post('/wallet/{id}/update', 'update')->name('wallet.update')->middleware('auth');
    Route::post('/wallet/{id}/toggle', 'toggleStatus')->name('wallet.toggle')->middleware('auth');
});

Route::controller(TransactionController::class)->group(function () {
    // Category routes
    Route::get('/transaction/category', 'category')->name('category')->middleware('auth');
    Route::get('/transaction/category/create', 'categoryCreate')->name('category.create')->middleware('auth');
    Route::post('/transaction/category/store', 'categoryStore')->name('category.store')->middleware('auth');
    Route::get('/transaction/category/{category}/edit', 'categoryEdit')->name('category.edit')->middleware('auth');
    Route::put('/transaction/category/{category}/update', 'categoryUpdate')->name('category.update')->middleware('auth');
    Route::delete('/transaction/category/{category}/destroy', 'categoryDestroy')->name('category.destroy')->middleware('auth');

    // Income routes
    Route::get('/transaction/income', 'income')->name('income')->middleware('auth');
    Route::get('/transaction/income/create', 'incomeCreate')->name('income.create')->middleware('auth');
    Route::post('/transaction/income/store', 'incomeStore')->name('income.store')->middleware('auth');
    Route::get('/transaction/income/{income}/edit', 'incomeEdit')->name('income.edit')->middleware('auth');
    Route::put('/transaction/income/{income}/update', 'incomeUpdate')->name('income.update')->middleware('auth');
    Route::delete('/transaction/income/{income}/destroy', 'incomeDestroy')->name('income.destroy')->middleware('auth');
    Route::put('/transaction/income/{income}/toggle', 'incomeToggle')->name('income.toggle')->middleware('auth');

    // Expense routes
    Route::get('/transaction/expense', 'expense')->name('expense')->middleware('auth');
    Route::get('/transaction/expense/create', 'expenseCreate')->name('expense.create')->middleware('auth');
    Route::post('/transaction/expense/store', 'expenseStore')->name('expense.store')->middleware('auth');
    Route::get('/transaction/expense/{expense}/edit', 'expenseEdit')->name('expense.edit')->middleware('auth');
    Route::put('/transaction/expense/{expense}/update', 'expenseUpdate')->name('expense.update')->middleware('auth');
    Route::delete('/transaction/expense/{expense}/destroy', 'expenseDestroy')->name('expense.destroy')->middleware('auth');
    Route::put('/transaction/expense/{expense}/toggle', 'expenseToggle')->name('expense.toggle')->middleware('auth');
});

Route::controller(BudgetController::class)->group(function () {
    Route::get('/budget', 'index')->name('budget')->middleware('auth');
    Route::get('/budget/create', 'create')->name('budget.create')->middleware('auth');
    Route::post('/budget/store', 'store')->name('budget.store')->middleware('auth');
    Route::get('/budget/{budget}/edit', 'edit')->name('budget.edit')->middleware('auth');
    Route::put('/budget/{budget}/update', 'update')->name('budget.update')->middleware('auth');
    Route::get('/budget/{budget}/detail', 'detail')->name('budget.detail')->middleware('auth');
    Route::post('/budget/{budget}/storeDetail', 'storeDetail')->name('budget.storeDetail')->middleware('auth');
    Route::put('/budget/{budget}/detail/{budgetDetail}', 'updateDetail')->name('budget.updateDetail')->middleware('auth');
    Route::get('/budget/{budgetDetail}/budgetDetail', 'budgetDetail')->name('budget.budgetDetail')->middleware('auth');
    Route::delete('/budget/{budgetDetail}/budgetDestroy', 'budgetDestroy')->name('budget.budgetDestroy')->middleware('auth');
});

Route::controller(StatementController::class)->group(function () {
    Route::get('/statement', 'index')->name('statement')->middleware('auth');
    Route::get('/statement/export/excel', 'exportExcel')->name('statement.export.excel')->middleware('auth');
    Route::get('/statement/export/pdf', 'exportPdf')->name('statement.export.pdf')->middleware('auth');
});