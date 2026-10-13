<?php

use App\Http\Controllers\Admin\AboutPageController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\ContactPageController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\ProductReviewController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\DeliveryChargeController;
use App\Http\Controllers\Admin\QuickContactController;
use App\Http\Controllers\Admin\SmtpSettingController;
use App\Http\Controllers\Admin\TelegramSettingController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\PromoBannerController;
use App\Http\Controllers\Admin\CustomScriptController;
use App\Http\Controllers\Admin\LogViewerController;
use App\Http\Controllers\Admin\PixelSettingController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Admin\SocialLinkController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\TopSellingController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {

    Route::middleware('admin')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('categories', CategoryController::class)->except('show');
        Route::patch('categories/{category}/toggle-home', [CategoryController::class, 'toggleHome'])->name('categories.toggle-home');

        // Homepage Category Sections (Ariomart Style)
        Route::get('homepage/category-sections', [\App\Http\Controllers\Admin\CategorySectionController::class, 'index'])->name('homepage.category-sections.index');
        Route::patch('homepage/category-sections/{category}/toggle', [\App\Http\Controllers\Admin\CategorySectionController::class, 'toggle'])->name('homepage.category-sections.toggle');
        Route::post('homepage/category-sections/reorder', [\App\Http\Controllers\Admin\CategorySectionController::class, 'reorder'])->name('homepage.category-sections.reorder');
        Route::put('homepage/category-sections/{category}/settings', [\App\Http\Controllers\Admin\CategorySectionController::class, 'updateSettings'])->name('homepage.category-sections.settings');

        Route::resource('brands', BrandController::class)->except('show');
        Route::resource('banners', BannerController::class)->except('show');
        Route::resource('promo-banners', PromoBannerController::class)->except('show')->parameters(['promo-banners' => 'banner']);
        Route::get('products/export', [ProductController::class, 'export'])->name('products.export');
        Route::get('products/check-sku', [ProductController::class, 'checkSku'])->name('products.check-sku');
        Route::resource('products', ProductController::class);
        Route::delete('products/{product}/images/{image}', [ProductController::class, 'destroyImage'])->name('products.images.destroy');

        Route::get('top-selling', [TopSellingController::class, 'edit'])->name('top-selling.edit');
        Route::put('top-selling', [TopSellingController::class, 'update'])->name('top-selling.update');
        Route::resource('testimonials', TestimonialController::class)->except('show');
        Route::patch('testimonials/{testimonial}/accept', [TestimonialController::class, 'accept'])->name('testimonials.accept');
        Route::patch('testimonials/{testimonial}/reject', [TestimonialController::class, 'reject'])->name('testimonials.reject');

        Route::get('settings', [SiteSettingController::class, 'index'])->name('settings.index');
        Route::get('settings/general', [SiteSettingController::class, 'edit'])->name('settings.general');
        Route::put('settings/general', [SiteSettingController::class, 'update'])->name('settings.update');
        Route::get('settings/edit', fn() => redirect()->route('admin.settings.general'))->name('settings.edit');

        // Laravel Logs (Settings Submenu)
        Route::get('logs', [LogViewerController::class, 'index'])->name('logs.index');
        Route::put('logs', [LogViewerController::class, 'update'])->name('logs.update');
        Route::post('logs/clear', [LogViewerController::class, 'clear'])->name('logs.clear');
        Route::get('logs/download', [LogViewerController::class, 'download'])->name('logs.download');
        Route::post('logs/test-entry', [LogViewerController::class, 'testEntry'])->name('logs.test-entry');
        // Pixel & Tracking Settings
        Route::get('pixel', [PixelSettingController::class, 'edit'])->name('pixel.edit');
        Route::put('pixel', [PixelSettingController::class, 'update'])->name('pixel.update');
        Route::post('pixel/test-capi', [PixelSettingController::class, 'testCapi'])->name('pixel.test-capi');

        // Site Scripts & Tracking (Header & Body code injection)
        Route::get('scripts', [CustomScriptController::class, 'edit'])->name('scripts.edit');
        Route::put('scripts', [CustomScriptController::class, 'update'])->name('scripts.update');

        Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');
        Route::get('customers/export', [CustomerController::class, 'export'])->name('customers.export');
        Route::get('customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
        Route::put('customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');

        Route::get('staff', [\App\Http\Controllers\Admin\StaffController::class, 'index'])->name('staff.index');
        Route::post('staff', [\App\Http\Controllers\Admin\StaffController::class, 'store'])->name('staff.store');

        Route::get('product-reviews', [ProductReviewController::class, 'index'])->name('product-reviews.index');
        Route::patch('product-reviews/{review}/approve', [ProductReviewController::class, 'approve'])->name('product-reviews.approve');
        Route::patch('product-reviews/{review}/reject', [ProductReviewController::class, 'reject'])->name('product-reviews.reject');
        Route::delete('product-reviews/{review}', [ProductReviewController::class, 'destroy'])->name('product-reviews.destroy');

        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/export', [ReportController::class, 'export'])->name('reports.export');

        Route::resource('coupons', CouponController::class)->except('show');

        Route::get('delivery-charge', [DeliveryChargeController::class, 'edit'])->name('delivery.edit');
        Route::put('delivery-charge', [DeliveryChargeController::class, 'update'])->name('delivery.update');

        Route::resource('pages', PageController::class)->except('show');

        Route::get('social-links', [SocialLinkController::class, 'edit'])->name('social-links.edit');
        Route::put('social-links', [SocialLinkController::class, 'update'])->name('social-links.update');

        Route::get('about-page', [AboutPageController::class, 'edit'])->name('about.edit');
        Route::put('about-page', [AboutPageController::class, 'update'])->name('about.update');

        Route::get('contact-page', [ContactPageController::class, 'edit'])->name('contact-page.edit');
        Route::put('contact-page', [ContactPageController::class, 'update'])->name('contact-page.update');

        Route::get('messages', [ContactMessageController::class, 'index'])->name('messages.index');
        Route::get('messages/{message}', [ContactMessageController::class, 'show'])->name('messages.show');
        Route::delete('messages/{message}', [ContactMessageController::class, 'destroy'])->name('messages.destroy');

        Route::get('quick-contact', [QuickContactController::class, 'edit'])->name('quick-contact.edit');
        Route::put('quick-contact', [QuickContactController::class, 'update'])->name('quick-contact.update');

        Route::get('telegram', [TelegramSettingController::class, 'edit'])->name('telegram.edit');
        Route::put('telegram', [TelegramSettingController::class, 'update'])->name('telegram.update');
        Route::post('telegram/test', [TelegramSettingController::class, 'test'])->name('telegram.test');

        Route::get('smtp', [SmtpSettingController::class, 'edit'])->name('smtp.edit');
        Route::put('smtp', [SmtpSettingController::class, 'update'])->name('smtp.update');
        Route::post('smtp/test', [SmtpSettingController::class, 'test'])->name('smtp.test');

        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/export', [OrderController::class, 'export'])->name('orders.export');
        Route::get('orders/create', [OrderController::class, 'create'])->name('orders.create');
        Route::post('orders', [OrderController::class, 'store'])->name('orders.store');
        Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::get('orders/{order}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');
        Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');
        Route::patch('orders/{order}/payment', [OrderController::class, 'updatePayment'])->name('orders.payment');
        Route::patch('orders/{order}/tracking', [OrderController::class, 'updateTracking'])->name('orders.tracking');
        Route::get('steadfast', [\App\Http\Controllers\Admin\SteadfastController::class, 'index'])->name('steadfast.index');
        Route::put('steadfast', [\App\Http\Controllers\Admin\SteadfastController::class, 'update'])->name('steadfast.update');
        Route::get('steadfast/track', [\App\Http\Controllers\Admin\SteadfastController::class, 'track'])->name('steadfast.track');
    });
});
