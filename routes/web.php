<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\ProductController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\CheckoutController;
use App\Http\Controllers\Frontend\CustomerController;
use App\Http\Controllers\Frontend\WishlistController;
use App\Http\Controllers\Frontend\SearchController;
use App\Http\Controllers\Frontend\PageController;
use App\Http\Controllers\Frontend\ReviewController;
use App\Http\Controllers\Frontend\NewsletterController;
use App\Http\Controllers\Frontend\ContactController;
use App\Http\Controllers\Frontend\BlogController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Frontend\ComplaintController;

/*
|--------------------------------------------------------------------------
| Frontend Routes
|--------------------------------------------------------------------------
*/

// Home
Route::get('/', [HomeController::class, 'index'])->name('home');

// Auth
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Products
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/shop', [ProductController::class, 'index'])->name('shop');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/category/{slug}', [ProductController::class, 'category'])->name('products.category');
Route::get('/brand/{slug}', [ProductController::class, 'brand'])->name('products.brand');
Route::get('/flash-sale', [ProductController::class, 'flashSale'])->name('products.flash-sale');

// Page aliases
Route::redirect('/about', '/page/about-us');
Route::redirect('/about-us', '/page/about-us');
Route::redirect('/privacy-policy', '/page/privacy-policy');
Route::redirect('/terms', '/page/terms-conditions');
Route::redirect('/terms-and-conditions', '/page/terms-conditions');
Route::redirect('/refund-policy', '/page/return-policy');
Route::redirect('/return-policy', '/page/return-policy');
Route::redirect('/shipping-policy', '/page/terms-conditions');

// Search
Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::get('/search/suggestions', [SearchController::class, 'suggestions'])->name('search.suggestions');

// Cart (AJAX)
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::patch('/update', [CartController::class, 'update'])->name('update');
    Route::delete('/remove/{id}', [CartController::class, 'remove'])->name('remove');
    Route::get('/count', [CartController::class, 'count'])->name('count');
    Route::get('/sidebar', [CartController::class, 'sidebar'])->name('sidebar');
    Route::post('/apply-coupon', [CartController::class, 'applyCoupon'])->name('apply-coupon');
    Route::delete('/remove-coupon', [CartController::class, 'removeCoupon'])->name('remove-coupon');
    Route::get('/restore/{token}', [CartController::class, 'restore'])->name('restore');
});

// Checkout
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout/update-lead', [CheckoutController::class, 'updateLead'])->name('checkout.update-lead');
Route::post('/checkout/place-order', [CheckoutController::class, 'placeOrder'])->name('checkout.place-order');
Route::get('/order-confirmation/{orderNumber}', [CheckoutController::class, 'confirmation'])->name('checkout.confirmation');

// Order Tracking
Route::get('/track-order', [CustomerController::class, 'trackOrderForm'])->name('track-order');
Route::post('/track-order', [CustomerController::class, 'trackOrder'])->name('track-order.search');

// Newsletter
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');

// Contact
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

// Pages
Route::get('/page/{slug}', [PageController::class, 'show'])->name('page.show');

// Complaints
Route::get('/complaints', [ComplaintController::class, 'create'])->name('complaints.create');
Route::post('/complaints', [ComplaintController::class, 'store'])->name('complaints.store');

// Wishlist (Both Guest and Auth)
Route::prefix('customer')->name('customer.')->group(function () {
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist');
    Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::post('/wishlist/move-to-cart', [WishlistController::class, 'moveToCart'])->name('wishlist.move-to-cart');
});

// Authenticated Customer Routes
Route::middleware('auth')->prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', [CustomerController::class, 'dashboard'])->name('dashboard');
    Route::get('/orders', [CustomerController::class, 'orders'])->name('orders');
    Route::get('/orders/{orderNumber}', [CustomerController::class, 'orderDetail'])->name('orders.detail');
    Route::get('/orders/{orderNumber}/invoice', [CustomerController::class, 'downloadInvoice'])->name('orders.invoice');
    Route::get('/profile', [CustomerController::class, 'profile'])->name('profile');
    Route::put('/profile', [CustomerController::class, 'updateProfile'])->name('profile.update');
    Route::get('/addresses', [CustomerController::class, 'addresses'])->name('addresses');
    Route::post('/addresses', [CustomerController::class, 'storeAddress'])->name('addresses.store');
    Route::put('/addresses/{id}', [CustomerController::class, 'updateAddress'])->name('addresses.update');
    Route::delete('/addresses/{id}', [CustomerController::class, 'deleteAddress'])->name('addresses.delete');

    // Reviews
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
});

// Blog routes
Route::get('/blogs', [BlogController::class, 'index'])->name('blogs.index');
Route::get('/blogs/{slug}', [BlogController::class, 'show'])->name('blogs.show');

