<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\StockBatchController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\StockOpnameController;
use App\Http\Controllers\SupplierReturnController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PasswordController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    
    // Dashboard - accessible by all authenticated users
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Password change (self)
    Route::get('/profile/password', [PasswordController::class, 'edit'])->name('profile.password.edit');
    Route::post('/profile/password', [PasswordController::class, 'update'])->name('profile.password.update');

    // Master Data - RBAC handled in controllers
    Route::get('suppliers/export', [SupplierController::class, 'export'])->name('suppliers.export');
    Route::resource('suppliers', SupplierController::class)->except(['show']);
    Route::get('products/export', [ProductController::class, 'export'])->name('products.export');
    Route::resource('products', ProductController::class)->except(['show']);

    // Shift Kasir & Z-Report
    Route::get('shifts', [ShiftController::class, 'index'])->name('shifts.index');
    Route::get('shifts/open', [ShiftController::class, 'create'])->name('shifts.create');
    Route::post('shifts/open', [ShiftController::class, 'store'])->name('shifts.store');
    Route::get('shifts/{shift}', [ShiftController::class, 'show'])->name('shifts.show');
    Route::get('shifts/{shift}/close', [ShiftController::class, 'closeForm'])->name('shifts.closeForm');
    Route::post('shifts/{shift}/close', [ShiftController::class, 'close'])->name('shifts.close');

    // POS Penjualan
    Route::get('sales/export', [SaleController::class, 'export'])->name('sales.export');
    Route::get('sales/{sale}/print', [SaleController::class, 'print'])->name('sales.print');
    Route::get('sales/{sale}/print-thermal', [SaleController::class, 'printThermal'])->name('sales.print-thermal');
    Route::post('sales/{sale}/cancel', [SaleController::class, 'cancel'])->name('sales.cancel')->middleware('role:owner');
    Route::resource('sales', SaleController::class)->only(['index', 'create', 'store', 'show']);
 
    // Pembelian / Penerimaan
    Route::get('purchases/export', [PurchaseController::class, 'export'])->name('purchases.export');
    Route::resource('purchases', PurchaseController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update']);

    // Stok
    Route::get('stock-batches', [StockBatchController::class, 'index'])->name('stock-batches.index');
    Route::get('stock-batches/create', [StockBatchController::class, 'create'])->name('stock-batches.create');
    Route::post('stock-batches', [StockBatchController::class, 'store'])->name('stock-batches.store');
    Route::get('stock-batches/{stock_batch}/edit', [StockBatchController::class, 'edit'])->name('stock-batches.edit');
    Route::put('stock-batches/{stock_batch}', [StockBatchController::class, 'update'])->name('stock-batches.update');

    Route::get('stock-movements', [StockMovementController::class, 'index'])->name('stock-movements.index');
    Route::get('stock-movements/export', [StockMovementController::class, 'export'])->name('stock-movements.export');
    Route::post('stock-movements', [StockMovementController::class, 'store'])->name('stock-movements.store');

    // Retur supplier
    Route::resource('supplier-returns', SupplierReturnController::class)->only(['index', 'create', 'store', 'show']);

    // Stock opname & approval
    Route::get('stock-opnames', [StockOpnameController::class, 'index'])->name('stock-opnames.index');
    Route::get('stock-opnames/create', [StockOpnameController::class, 'create'])->name('stock-opnames.create');
    Route::post('stock-opnames', [StockOpnameController::class, 'store'])->name('stock-opnames.store');
    Route::get('stock-opnames/{stock_opname}', [StockOpnameController::class, 'show'])->name('stock-opnames.show');
    Route::post('stock-opnames/{stock_opname}/approve', [StockOpnameController::class, 'approve'])->name('stock-opnames.approve');

    // Laporan & PDF/email
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/pdf', [ReportController::class, 'pdf'])->name('reports.pdf');
    Route::post('reports/email', [ReportController::class, 'email'])->name('reports.email');
    
    // Owner routes
    Route::middleware(['role:owner'])->group(function () {
        Route::resource('users', UserController::class)->except(['show', 'destroy']);
        Route::post('users/{user}/disable', [UserController::class, 'disable'])->name('users.disable');
        Route::get('backup', [BackupController::class, 'index'])->name('backup.index');
        Route::post('backup', [BackupController::class, 'store'])->name('backup.store');
    });
    
    // Kasir routes
    Route::middleware(['role:kasir'])->prefix('kasir')->name('kasir.')->group(function () {
        // Kasir specific routes will be added here
    });
    
    // Admin Gudang routes
    Route::middleware(['role:admin_gudang'])->prefix('gudang')->name('gudang.')->group(function () {
        // Admin gudang specific routes will be added here
    });
});
