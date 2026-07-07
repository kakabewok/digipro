<?php

use App\Http\Controllers\PaymentWebhookController;
use Illuminate\Support\Facades\Route;

Route::get('/', App\Livewire\Landing\HomePage::class)->name('home');

// Payment webhook (no auth, CSRF excluded in bootstrap/app.php)
Route::post('webhook/payment', [PaymentWebhookController::class, 'handle'])
    ->name('webhook.payment');

// Authenticated customer routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Customer Dashboard
    Route::get('dashboard', \App\Livewire\Customer\Dashboard::class)->name('dashboard');

    // Products
    Route::get('products', \App\Livewire\Customer\ProductList::class)->name('products.index');
    Route::get('products/{slug}', \App\Livewire\Customer\ProductDetail::class)->name('products.show');

    // Checkout
    Route::get('checkout/{slug}', \App\Livewire\Customer\Checkout::class)->name('checkout');

    // Orders / Transactions
    Route::get('orders', \App\Livewire\Customer\TransactionHistory::class)->name('orders.index');
    Route::get('orders/{invoice}', \App\Livewire\Customer\TransactionDetail::class)->name('orders.show');

    // Deposits
    Route::get('deposits', \App\Livewire\Customer\DepositHistory::class)->name('deposits.index');
    Route::get('deposits/create', \App\Livewire\Customer\Deposit::class)->name('deposits.create');

    // Profile
    Route::get('profile/edit', \App\Livewire\Customer\EditProfile::class)->name('profile.edit.custom');
});

// Admin routes
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', \App\Livewire\Admin\Dashboard::class)->name('dashboard');

    // Products
    Route::get('products', \App\Livewire\Admin\Products\Index::class)->name('products.index');
    Route::get('products/create', \App\Livewire\Admin\Products\Create::class)->name('products.create');
    Route::get('products/{id}/edit', \App\Livewire\Admin\Products\Edit::class)->name('products.edit');

    // Categories
    Route::get('categories', \App\Livewire\Admin\Categories\Index::class)->name('categories.index');

    // Stocks
    Route::get('stocks', \App\Livewire\Admin\Stocks\Index::class)->name('stocks.index');
    Route::get('stocks/import', \App\Livewire\Admin\Stocks\Import::class)->name('stocks.import');

    // Vouchers
    Route::get('vouchers', \App\Livewire\Admin\Vouchers\Index::class)->name('vouchers.index');

    // Users
    Route::get('users', \App\Livewire\Admin\Users\Index::class)->name('users.index');

    // Deposits
    Route::get('deposits', \App\Livewire\Admin\Deposits\Index::class)->name('deposits.index');

    // Orders
    Route::get('orders', \App\Livewire\Admin\Orders\Index::class)->name('orders.index');
    Route::get('orders/{id}', \App\Livewire\Admin\Orders\Detail::class)->name('orders.detail');

    // Settings
    Route::get('settings', \App\Livewire\Admin\Settings\Index::class)->name('settings.index');

    // Backup
    Route::get('backup', \App\Livewire\Admin\Backup\Index::class)->name('backup.index');

    // Logs
    Route::get('activity-logs', \App\Livewire\Admin\Logs\ActivityLog::class)->name('logs.activity');
    Route::get('audit-logs', \App\Livewire\Admin\Logs\AuditLog::class)->name('logs.audit');
});

require __DIR__.'/settings.php';
