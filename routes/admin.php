<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\ComplaintController;
use App\Http\Controllers\Admin\CampaignController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ShippingZoneController;
use App\Http\Controllers\Admin\TaxController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\NewsletterController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\BackupController;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/analytics', [DashboardController::class, 'analytics'])->name('analytics');

    // Categories
    Route::post('categories/bulk-delete', [CategoryController::class, 'bulkDelete'])->name('categories.bulk-delete');
    Route::resource('categories', CategoryController::class);
    Route::post('categories/reorder', [CategoryController::class, 'reorder'])->name('categories.reorder');

    // Brands
    Route::post('brands/bulk-delete', [BrandController::class, 'bulkDelete'])->name('brands.bulk-delete');
    Route::resource('brands', BrandController::class);

    // Attributes
    Route::resource('attributes', AttributeController::class);
    Route::post('attributes/{attribute}/values', [AttributeController::class, 'storeValue'])->name('attributes.values.store');
    Route::delete('attributes/values/{value}', [AttributeController::class, 'destroyValue'])->name('attributes.values.destroy');

    // Products
    Route::post('products/bulk-action', [ProductController::class, 'bulkAction'])->name('products.bulk-action');
    Route::post('products/{product}/clone', [ProductController::class, 'clone'])->name('products.clone');
    Route::resource('products', ProductController::class);
    Route::post('products/{product}/images', [ProductController::class, 'uploadImages'])->name('products.images.upload');
    Route::delete('products/images/{image}', [ProductController::class, 'deleteImage'])->name('products.images.delete');
    Route::post('products/{product}/variants', [ProductController::class, 'storeVariant'])->name('products.variants.store');
    Route::delete('products/variants/{variant}', [ProductController::class, 'deleteVariant'])->name('products.variants.delete');

    // Inventory Management
    Route::get('inventory', [App\Http\Controllers\Admin\InventoryController::class, 'index'])->name('inventory.index');
    Route::post('inventory/restock', [App\Http\Controllers\Admin\InventoryController::class, 'restock'])->name('inventory.restock');
    Route::get('inventory/valuation', [App\Http\Controllers\Admin\InventoryController::class, 'valuation'])->name('inventory.valuation');

    // Orders
    Route::get('orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('orders/store', [OrderController::class, 'store'])->name('orders.store');
    Route::get('orders/incomplete', [\App\Http\Controllers\Admin\IncompleteOrderController::class, 'incomplete'])->name('orders.incomplete');
    Route::post('orders/push-courier', [OrderController::class, 'pushToCourier'])->name('orders.push-courier');
    Route::post('orders/bulk-status', [OrderController::class, 'bulkStatusUpdate'])->name('orders.bulk-status');
    Route::post('orders/bulk-delete', [OrderController::class, 'bulkDelete'])->name('orders.bulk-delete');
    Route::post('orders/{order}/settle-courier', [OrderController::class, 'settleCourier'])->name('orders.settle-courier');
    Route::post('orders/{order}/track-parcel', [OrderController::class, 'trackParcel'])->name('orders.track-parcel');
    Route::resource('orders', OrderController::class)->only(['index', 'show', 'update', 'destroy']);
    Route::post('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');
    Route::get('orders/{order}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');

    // Abandoned Cart Management & Recovery Console
    Route::prefix('abandoned-carts')->name('abandoned-carts.')->group(function () {
        Route::get('/{cart}/json', [\App\Http\Controllers\Admin\IncompleteOrderController::class, 'showJson'])->name('json');
        Route::post('/{cart}/status', [\App\Http\Controllers\Admin\IncompleteOrderController::class, 'updateStatus'])->name('status');
        Route::post('/{cart}/create-order', [\App\Http\Controllers\Admin\IncompleteOrderController::class, 'createOrder'])->name('create-order');
        Route::post('/{cart}/send-sms', [\App\Http\Controllers\Admin\IncompleteOrderController::class, 'sendSms'])->name('send-sms');
        Route::post('/{cart}/send-email', [\App\Http\Controllers\Admin\IncompleteOrderController::class, 'sendEmail'])->name('send-email');
        Route::post('/bulk-action', [\App\Http\Controllers\Admin\IncompleteOrderController::class, 'bulkAction'])->name('bulk-action');
        Route::get('/analytics/report', [\App\Http\Controllers\Admin\IncompleteOrderController::class, 'analytics'])->name('analytics');
    });

    // Courier Settings
    Route::get('couriers/settings', [\App\Http\Controllers\Admin\CourierSettingController::class, 'index'])->name('couriers.settings');
    Route::post('couriers/settings', [\App\Http\Controllers\Admin\CourierSettingController::class, 'update'])->name('couriers.settings.update');
    Route::post('couriers/test-connection', [\App\Http\Controllers\Admin\CourierSettingController::class, 'testConnection'])->name('couriers.test-connection');

    // Customers
    Route::post('customers/bulk-delete', [CustomerController::class, 'bulkDelete'])->name('customers.bulk-delete');
    Route::resource('customers', CustomerController::class)->only(['index', 'show', 'edit', 'update', 'destroy']);

    // Coupons
    Route::post('coupons/bulk-delete', [CouponController::class, 'bulkDelete'])->name('coupons.bulk-delete');
    Route::resource('coupons', CouponController::class);

    // Campaigns
    Route::resource('campaigns', CampaignController::class);

    // Sliders
    Route::post('sliders/bulk-delete', [SliderController::class, 'bulkDelete'])->name('sliders.bulk-delete');
    Route::resource('sliders', SliderController::class);

    // Banners
    Route::post('banners/bulk-delete', [BannerController::class, 'bulkDelete'])->name('banners.bulk-delete');
    Route::resource('banners', BannerController::class);

    // Pages
    Route::post('pages/bulk-delete', [PageController::class, 'bulkDelete'])->name('pages.bulk-delete');
    Route::resource('pages', PageController::class);

    // Blogs
    Route::post('blogs/bulk-delete', [BlogController::class, 'bulkDelete'])->name('blogs.bulk-delete');
    Route::resource('blogs', BlogController::class);

    // Menus
    Route::post('menus/bulk-delete', [MenuController::class, 'bulkDelete'])->name('menus.bulk-delete');
    Route::resource('menus', MenuController::class);

    // Testimonials
    Route::post('testimonials/bulk-delete', [TestimonialController::class, 'bulkDelete'])->name('testimonials.bulk-delete');
    Route::resource('testimonials', TestimonialController::class);

    // FAQs
    Route::post('faqs/bulk-delete', [FaqController::class, 'bulkDelete'])->name('faqs.bulk-delete');
    Route::resource('faqs', FaqController::class);

    // Reviews
    Route::post('reviews/bulk-delete', [ReviewController::class, 'bulkDelete'])->name('reviews.bulk-delete');
    Route::resource('reviews', ReviewController::class)->only(['index', 'show', 'update', 'destroy']);

    // Complaints
    Route::post('complaints/bulk-delete', [ComplaintController::class, 'bulkDelete'])->name('complaints.bulk-delete');
    Route::resource('complaints', ComplaintController::class)->only(['index', 'show', 'update', 'destroy']);

    // Shipping Zones
    Route::post('shipping-zones/bulk-delete', [ShippingZoneController::class, 'bulkDelete'])->name('shipping-zones.bulk-delete');
    Route::resource('shipping-zones', ShippingZoneController::class);

    // Payments
    Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');

    // Notifications
    Route::post('notifications/read-all', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    })->name('notifications.read-all');

    // Taxes
    Route::post('taxes/bulk-delete', [TaxController::class, 'bulkDelete'])->name('taxes.bulk-delete');
    Route::resource('taxes', TaxController::class);

    // Media Library
    Route::get('media', [MediaController::class, 'index'])->name('media.index');
    Route::post('media/upload', [MediaController::class, 'upload'])->name('media.upload');
    Route::post('media/clean-unused', [MediaController::class, 'cleanUnused'])->name('media.clean-unused');
    Route::delete('media/{media}', [MediaController::class, 'destroy'])->name('media.destroy');

    // Financial Management
    Route::get('financial', [App\Http\Controllers\Admin\FinancialController::class, 'index'])->name('financial.index');
    Route::post('financial', [App\Http\Controllers\Admin\FinancialController::class, 'store'])->name('financial.store');

    // Accounting Module (হিসাব-নিকাশ)
    Route::prefix('accounting')->name('accounting.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\AccountingController::class, 'dashboard'])->name('dashboard');
        Route::get('/money-in', [App\Http\Controllers\Admin\AccountingController::class, 'moneyIn'])->name('money-in');
        Route::get('/money-out', [App\Http\Controllers\Admin\AccountingController::class, 'moneyOut'])->name('money-out');
        Route::post('/expenses', [App\Http\Controllers\Admin\AccountingController::class, 'storeExpense'])->name('expenses.store');
        Route::get('/sales-profit', [App\Http\Controllers\Admin\AccountingController::class, 'salesProfit'])->name('sales-profit');
        Route::get('/purchases', [App\Http\Controllers\Admin\AccountingController::class, 'purchases'])->name('purchases.index');
        Route::post('/purchases', [App\Http\Controllers\Admin\AccountingController::class, 'storePurchase'])->name('purchases.store');
        Route::get('/customer-due', [App\Http\Controllers\Admin\AccountingController::class, 'customerDue'])->name('customer-due');
        Route::post('/orders/{order}/collect-due', [App\Http\Controllers\Admin\AccountingController::class, 'collectOrderDue'])->name('orders.collect-due');
        Route::get('/supplier-due', [App\Http\Controllers\Admin\AccountingController::class, 'supplierDue'])->name('supplier-due');
        Route::post('/purchases/{purchase}/pay-due', [App\Http\Controllers\Admin\AccountingController::class, 'paySupplierDue'])->name('purchases.pay-due');
        Route::get('/accounts', [App\Http\Controllers\Admin\AccountingController::class, 'accounts'])->name('accounts.index');
        Route::post('/accounts', [App\Http\Controllers\Admin\AccountingController::class, 'storeAccount'])->name('accounts.store');
        Route::get('/investments', [App\Http\Controllers\Admin\AccountingController::class, 'investments'])->name('investments.index');
        Route::post('/investments', [App\Http\Controllers\Admin\AccountingController::class, 'storeInvestment'])->name('investments.store');
        Route::get('/salaries', [App\Http\Controllers\Admin\AccountingController::class, 'salaries'])->name('salaries.index');
        Route::post('/salaries', [App\Http\Controllers\Admin\AccountingController::class, 'storeSalary'])->name('salaries.store');
        Route::get('/owner-withdrawals', [App\Http\Controllers\Admin\AccountingController::class, 'ownerWithdrawals'])->name('owner-withdrawals.index');
        Route::post('/owner-withdrawals', [App\Http\Controllers\Admin\AccountingController::class, 'storeWithdrawal'])->name('owner-withdrawals.store');
        Route::get('/courier-settlements', [App\Http\Controllers\Admin\AccountingController::class, 'courierSettlements'])->name('courier-settlements');
        Route::get('/profit-loss', [App\Http\Controllers\Admin\AccountingController::class, 'profitLoss'])->name('profit-loss');
        Route::get('/inventory-value', [App\Http\Controllers\Admin\AccountingController::class, 'inventoryValue'])->name('inventory-value');
        Route::get('/monthly-closing', [App\Http\Controllers\Admin\AccountingController::class, 'monthlyClosing'])->name('monthly-closing');
        Route::get('/reports', [App\Http\Controllers\Admin\AccountingController::class, 'reports'])->name('reports.index');
        Route::get('/reports/export/{type}', [App\Http\Controllers\Admin\AccountingController::class, 'exportReport'])->name('reports.export');
        Route::get('/transactions', [App\Http\Controllers\Admin\AccountingController::class, 'transactions'])->name('transactions.index');
        Route::post('/transactions/{transaction}/reverse', [App\Http\Controllers\Admin\AccountingController::class, 'reverseTransaction'])->name('transactions.reverse');
    });


    // Settings
    Route::get('settings/{group?}', [SettingController::class, 'index'])->name('settings.index');
    Route::post('settings/{group}', [SettingController::class, 'update'])->name('settings.update');

    // Pixel & Analytics Configuration
    Route::get('analytics-config', [App\Http\Controllers\Admin\AnalyticsConfigController::class, 'index'])->name('analytics-config.index');
    Route::post('analytics-config', [App\Http\Controllers\Admin\AnalyticsConfigController::class, 'update'])->name('analytics-config.update');

    // Database Backup & Restore
    Route::get('backup', [BackupController::class, 'index'])->name('backup.index');
    Route::post('backup/run', [BackupController::class, 'runBackup'])->name('backup.run');
    Route::post('backup/restore', [BackupController::class, 'runRestore'])->name('backup.restore');
    Route::get('backup/download', [BackupController::class, 'downloadBackup'])->name('backup.download');
    Route::post('backup/upload', [BackupController::class, 'uploadBackup'])->name('backup.upload');
    Route::post('backup/reset', [BackupController::class, 'resetDb'])->name('backup.reset');
    Route::post('backup/clear-products', [BackupController::class, 'clearProducts'])->name('backup.clear-products');
    Route::get('backup/csrf-token', [BackupController::class, 'csrfToken'])->name('backup.csrf-token');

    // Users & Roles
    Route::post('users/bulk-delete', [UserController::class, 'bulkDelete'])->name('users.bulk-delete');
    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);

    // Contacts
    Route::post('contacts/bulk-delete', [ContactController::class, 'bulkDelete'])->name('contacts.bulk-delete');
    Route::resource('contacts', ContactController::class)->only(['index', 'show', 'destroy']);
    Route::patch('contacts/{contact}/status', [ContactController::class, 'updateStatus'])->name('contacts.status');

    // Newsletter
    Route::get('newsletters', [NewsletterController::class, 'index'])->name('newsletters.index');
    Route::delete('newsletters/{newsletter}', [NewsletterController::class, 'destroy'])->name('newsletters.destroy');
    Route::get('newsletters/export', [NewsletterController::class, 'export'])->name('newsletters.export');

    // Reports
    Route::get('reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
    Route::get('reports/products', [ReportController::class, 'products'])->name('reports.products');
    Route::get('reports/customers', [ReportController::class, 'customers'])->name('reports.customers');
    Route::get('reports/export/{type}', [ReportController::class, 'export'])->name('reports.export');

    // Activity Logs
    Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
});
