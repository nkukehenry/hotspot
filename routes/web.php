<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/', [CustomerController::class, 'showLocations'])->name('customer.index');
Route::get('/contact', [CustomerController::class, 'showLocations'])->name('customer.index');
Route::get('/locations', [CustomerController::class, 'showLocations'])->name('customer.locations');
Route::get('/wifi/{location}', [CustomerController::class, 'showPackages'])->name('customer.packages');
//Route::get('/packages/{location}', [CustomerController::class, 'showPackages'])->name('customer.packages');
Route::get('/payment/{package}', [CustomerController::class, 'showPayment'])->name('customer.payment');
Route::post('/payment/{package}', [CustomerController::class, 'processPayment'])->name('customer.processPayment');
Route::get('/voucher/{transactionId}', [CustomerController::class, 'showVoucher'])->name('customer.voucher');
Route::get('/transactions', [CustomerController::class, 'showTransactions'])->name('customer.transactions');
Route::post('/jpesa/callback',[CustomerController::class, 'handleCallback'])->middleware('exclude.cors');;

Route::group(['middleware' => 'auth', 'prefix' => 'admin'], function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/locations', [AdminController::class, 'showLocations'])->name('admin.locations');
    Route::get('/packages', [AdminController::class, 'showPackages'])->name('admin.packages');
    Route::get('/upload-vouchers', [AdminController::class, 'showUploadVouchers'])->name('admin.showUploadVouchers');
    Route::post('/upload-vouchers', [AdminController::class, 'uploadVouchers'])->name('admin.uploadVouchers');
    Route::post('/packages', [AdminController::class, 'createPackage'])->name('admin.addPackage');
    Route::put('/packages/{package}', [AdminController::class, 'updatePackage'])->name('admin.updatePackage');
    Route::delete('/packages/{package}', [AdminController::class, 'deletePackage'])->name('admin.deletePackage');
    Route::get('/users', [AdminController::class, 'showUsers'])->name('admin.users');
    Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('admin.updateUser');
    Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('admin.deleteUser');
    Route::get('/reports', [AdminController::class, 'showReports'])->name('admin.reports');
    Route::get('/settings', [AdminController::class, 'showSettings'])->name('admin.settings');
    Route::post('/settings', [AdminController::class, 'updateSettings'])->name('admin.updateSettings');
    Route::post('/locations', [AdminController::class, 'createLocation'])->name('admin.addLocation');
    Route::put('/locations/{location}', [AdminController::class, 'updateLocation'])->name('admin.updateLocation');
    Route::delete('/locations/{location}', [AdminController::class, 'deleteLocation'])->name('admin.deleteLocation');
    Route::get('/vouchers', [AdminController::class, 'showVouchers'])->name('admin.vouchers');
    Route::get('/packages/{locationId}', [AdminController::class, 'getPackagesByLocation'])->name('admin.locationPackages');
    Route::post('/admin/vouchers/bulk-action', [AdminController::class, 'bulkAction'])->name('admin.vouchers.bulkAction');
    Route::post('/users', [UserController::class, 'store'])->name('admin.addUser');
});

require __DIR__ . '/auth.php';
