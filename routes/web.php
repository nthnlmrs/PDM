<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\RefundController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\WithdrawalController;
use App\Http\Controllers\Admin\CategoryController;

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/product/{product:slug}', [HomeController::class, 'show'])->name('product.show');

// Auth Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Buyer Routes
    Route::post('/buy/{product}', [OrderController::class, 'store'])->name('order.store');
    Route::get('/my-orders', [OrderController::class, 'index'])->name('order.index');
    Route::post('/orders/{order}/confirm', [OrderController::class, 'confirm'])->name('order.confirm');
    
    // Refund Routes
    Route::post('/orders/{order}/refund', [RefundController::class, 'store'])->name('refund.store');
    Route::post('/refunds/{refund}/escalate', [RefundController::class, 'escalate'])->name('refund.escalate');

    // Cart Routes
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/{product}', [CartController::class, 'store'])->name('cart.store');
    Route::patch('/cart/{cart}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{cart}', [CartController::class, 'destroy'])->name('cart.destroy');
    Route::get('/cart/checkout/success', [CartController::class, 'success'])->name('cart.success');
    Route::post('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
    Route::post('/cart/apply-voucher', [CartController::class, 'applyVoucher'])->name('cart.applyVoucher');

    // Order - Buyer Confirm Receipt
    Route::post('/orders/{order}/confirm-receipt', [OrderController::class, 'confirmReceipt'])->name('order.confirmReceipt');

    // Wishlist Routes
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/{product}', [WishlistController::class, 'store'])->name('wishlist.store');
    Route::delete('/wishlist/{wishlist}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');
    Route::post('/wishlist/{product}/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

    // Review Routes
    Route::post('/products/{product}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::patch('/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

    // Seller Request
    Route::get('/become-seller', [\App\Http\Controllers\SellerRequestController::class, 'create'])->name('seller.request.create');
    Route::post('/become-seller', [\App\Http\Controllers\SellerRequestController::class, 'store'])->name('seller.request.store');

    // Dashboard Redirector
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// Seller Routes
Route::middleware(['auth', 'role:seller'])->prefix('seller')->name('seller.')->group(function () {
    Route::get('/dashboard', function() { return redirect()->route('seller.products.index'); })->name('dashboard');
    Route::resource('products', ProductController::class);
    Route::get('/orders', [\App\Http\Controllers\SellerOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [\App\Http\Controllers\SellerOrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/tracking', [\App\Http\Controllers\SellerOrderController::class, 'updateTracking'])->name('orders.tracking');
    Route::get('/refunds', [RefundController::class, 'sellerIndex'])->name('refunds.index');
    Route::post('/refunds/{refund}/decide', [RefundController::class, 'sellerDecide'])->name('refunds.decide');

    // Withdrawal Routes
    Route::get('/withdrawals', [WithdrawalController::class, 'index'])->name('withdrawals.index');
    Route::get('/withdrawals/create', [WithdrawalController::class, 'create'])->name('withdrawals.create');
    Route::post('/withdrawals', [WithdrawalController::class, 'store'])->name('withdrawals.store');
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::post('/users/{user}/topup', [AdminController::class, 'topup'])->name('users.topup');
    Route::post('/users/{user}/role', [AdminController::class, 'updateRole'])->name('users.role');
    Route::get('/refunds', [AdminController::class, 'refunds'])->name('refunds');
    Route::post('/refunds/{refund}/resolve', [AdminController::class, 'resolveRefund'])->name('refunds.resolve');
    Route::get('/requests', [AdminController::class, 'requests'])->name('requests');
    Route::post('/requests/{request}/approve', [AdminController::class, 'approveRequest'])->name('requests.approve');

    // Suspend & Tickets
    Route::post('/users/{user}/suspend', [AdminController::class, 'toggleSuspend'])->name('users.suspend');
    Route::get('/tickets', [AdminController::class, 'tickets'])->name('tickets');
    Route::post('/tickets/{ticket}/resolve', [AdminController::class, 'resolveTicket'])->name('tickets.resolve');

    // Product Moderation
    Route::get('/products', [AdminController::class, 'products'])->name('products');
    Route::post('/products/{product}/suspend', [AdminController::class, 'toggleProductSuspend'])->name('products.suspend');

    // Category Management
    Route::resource('categories', CategoryController::class);

    // Voucher Management
    Route::resource('vouchers', \App\Http\Controllers\Admin\VoucherController::class);

    // Withdrawal Management
    Route::get('/withdrawals', [AdminController::class, 'withdrawals'])->name('withdrawals');
    Route::post('/withdrawals/{withdrawal}/process', [AdminController::class, 'processWithdrawal'])->name('withdrawals.process');
});

// Suspended Route
Route::middleware(['auth'])->group(function () {
    Route::get('/suspended', [\App\Http\Controllers\TicketController::class, 'showSuspended'])->name('suspended.show');
    Route::post('/tickets', [\App\Http\Controllers\TicketController::class, 'store'])->name('tickets.store');
});

require __DIR__.'/auth.php';

