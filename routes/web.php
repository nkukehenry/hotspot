<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SiteController;
use App\Services\SMSService;
use Illuminate\Support\Facades\Route;
use App\Jobs\SendWhatsAppJob;


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/', [CustomerController::class, 'showSites'])->name('customer.index');
Route::get('/contact', [CustomerController::class, 'showSites'])->name('customer.index');
Route::get('/sites', [CustomerController::class, 'showSites'])->name('customer.sites');
Route::get('/wifi/{site}', [CustomerController::class, 'showPackages'])->name('customer.packages');
//Route::get('/packages/{site}', [CustomerController::class, 'showPackages'])->name('customer.packages');
Route::get('/payment/{package}', [CustomerController::class, 'showPayment'])->name('customer.payment');
Route::post('/payment/{package}', [CustomerController::class, 'processPayment'])->name('customer.processPayment');
Route::get('/voucher/{transaction}', [CustomerController::class, 'showVoucher'])->name('customer.voucher');
Route::get('/transactions', [CustomerController::class, 'showTransactions'])->name('customer.transactions');
Route::any('/jpesa/callback',[CustomerController::class, 'handleCallback']);

Route::group(['middleware' => ['auth', 'role:Owner|Manager|Supervisor|Agent'], 'prefix' => 'admin'], function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // Site Management - Only Owner can manage sites
    Route::resource('sites', SiteController::class)->middleware(['role:Owner'])->names([
        'index' => 'admin.sites',
        'store' => 'admin.addSite',
        'update' => 'admin.updateSite',
        'show' => 'admin.site.details', // Changed to admin.site.details as requested by user or just descriptive
        'destroy' => 'admin.deleteSite',
    ]);

    Route::get('/packages', [AdminController::class, 'showPackages'])->name('admin.packages');
    Route::get('/upload-vouchers', [AdminController::class, 'showUploadVouchers'])->name('admin.showUploadVouchers');
    Route::post('/upload-vouchers', [AdminController::class, 'uploadVouchers'])->name('admin.uploadVouchers');
    Route::post('/packages', [AdminController::class, 'createPackage'])->name('admin.addPackage');
    Route::put('/packages/{package}', [AdminController::class, 'updatePackage'])->name('admin.updatePackage');
    Route::delete('/packages/{package}', [AdminController::class, 'deletePackage'])->name('admin.deletePackage');
    
    // User Management
    Route::get('/users', [UserController::class, 'index'])->name('admin.users');
    Route::post('/users', [UserController::class, 'store'])->name('admin.addUser');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('admin.updateUser');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('admin.deleteUser');

    Route::get('/reports', [AdminController::class, 'showReports'])->name('admin.reports');
    Route::get('/transactions', [AdminController::class, 'showTransactions'])->name('admin.transactions'); // Added transactions route
    Route::get('/reconciliation', [AdminController::class, 'showReconciliation'])->name('admin.reconciliation');
    Route::post('/reconcile', [AdminController::class, 'reconcileCash'])->name('admin.reconcile');
    Route::get('/settings', [AdminController::class, 'showSettings'])->name('admin.settings');
    Route::post('/settings', [AdminController::class, 'updateSettings'])->name('admin.updateSettings');
    
    Route::get('/vouchers', [AdminController::class, 'showVouchers'])->name('admin.vouchers');
    Route::get('/packages/{siteId}', [AdminController::class, 'getPackagesBySite'])->name('admin.sitePackages');
    Route::post('/admin/vouchers/bulk-action', [AdminController::class, 'bulkAction'])->name('admin.vouchers.bulkAction');

    // Role & Permission Management - Owner Only
    Route::middleware(['role:Owner'])->group(function () {
        Route::get('/roles', [App\Http\Controllers\Admin\RoleController::class, 'index'])->name('admin.roles.index');
        Route::get('/roles/create', [App\Http\Controllers\Admin\RoleController::class, 'create'])->name('admin.roles.create');
        Route::post('/roles', [App\Http\Controllers\Admin\RoleController::class, 'store'])->name('admin.roles.store');
        Route::get('/roles/{role}/edit', [App\Http\Controllers\Admin\RoleController::class, 'edit'])->name('admin.roles.edit');
        Route::put('/roles/{role}', [App\Http\Controllers\Admin\RoleController::class, 'update'])->name('admin.roles.update');
        Route::delete('/roles/{role}', [App\Http\Controllers\Admin\RoleController::class, 'destroy'])->name('admin.roles.destroy');
    });
});

// Agent Portal - Mobile First
Route::middleware(['auth', 'role:Agent'])->prefix('agent')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Agent\AgentController::class, 'index'])->name('agent.dashboard');
    Route::post('/sell', [App\Http\Controllers\Agent\AgentController::class, 'sell'])->name('agent.sell.store');
});

Route::get('/test',function(SMSService $sMSService){
    $sMSService->sendVoucher("256777245670","988878787");
    // This returns immediately, processing happens in background
   SendWhatsAppJob::dispatch('256777245670', 'Hello from Neonet!');
});



require __DIR__ . '/auth.php';
