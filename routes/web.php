<?php

use App\Http\Controllers\Callbacks\GoogleAuthController;
use App\Models\Order;
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

Route::get('invoice', function () {
    $order = Order::all()->last();
    $service = new \App\Services\InvoicesService();

    dd($service->generate($order)->url());
});

Route::get('/', \App\Http\Controllers\HomeController::class)->name('home');

Auth::routes();

Route::name('ajax.')->prefix('ajax')->group(function () {
    Route::group(['auth', 'role:admin|moderator'], function () {
        Route::post('products/{product:slug}/image', \App\Http\Controllers\Ajax\Products\UploadImage::class)->name('products.image.upload');
        Route::delete('images/{image}', \App\Http\Controllers\Ajax\RemoveImageController::class)->name('image.remove');
    });

    Route::prefix('paypal')->name('paypal.')->group(function () {
        Route::post('order/create', [\App\Http\Controllers\Ajax\Payments\PaypalController::class, 'create'])->name('create'); // ajax.paypal.create
        Route::post('order/{vendorOrderId}/capture', [\App\Http\Controllers\Ajax\Payments\PaypalController::class, 'capture'])->name('capture'); // ajax.paypal.capture
    });
});

Route::resource('products', \App\Http\Controllers\ProductsController::class)->only(['index', 'show']);
Route::resource('categories', \App\Http\Controllers\CategoriesController::class)->only(['index', 'show']);

Route::name('cart.')->prefix('cart')->group(function () {
    Route::get('/', [\App\Http\Controllers\CartController::class, 'index'])->name('index');
    Route::post('{product}', [\App\Http\Controllers\CartController::class, 'add'])->name('add');
    Route::delete('/', [\App\Http\Controllers\CartController::class, 'remove'])->name('remove');
    Route::post('{product}/count', [\App\Http\Controllers\CartController::class, 'count'])->name('count');
});

Route::get('checkout', \App\Http\Controllers\CheckoutController::class)->name('checkout');
Route::get('orders/{vendorOrderId}/thank-you', \App\Http\Controllers\Pages\ThankYouController::class)->name('thankyou');

Route::name('callbacks.')->prefix('callbacks')->group(function () {
    Route::get('telegram', \App\Http\Controllers\Callbacks\JoinTelegramController::class)
        ->middleware(['role:admin'])
        ->name('telegram');
});

Route::name('admin.')->prefix('admin')->middleware('role:admin|moderator')->group(function () {
    Route::get('/', \App\Http\Controllers\Admin\DashboardController::class)->name('dashboard');
    Route::resource('categories', \App\Http\Controllers\Admin\CategoriesController::class)->except(['show']);
    Route::resource('products', \App\Http\Controllers\Admin\ProductsController::class)->except(['show']);
});

Route::middleware('auth')->group(function () {
    Route::prefix('wishlist')->name('wishlist.')->group(function () {
        Route::post('{product}', [\App\Http\Controllers\WishListController::class, 'add'])->name('add');
        Route::delete('{product}', [\App\Http\Controllers\WishListController::class, 'remove'])->name('remove');
    });

    Route::name('account.')->prefix('account')->group(function () {
        Route::get('wishlist', \App\Http\Controllers\Admin\WishListController::class)->name('wishlist');
    });

    Route::get('invoices/{order}', \App\Http\Controllers\InvoicesController::class)->name('invoice');
});

Route::get('notify-admin', function () {
    \App\Events\Admin\NotifyEvent::dispatch('message from event to websocket');
});

Route::get('csv', function () {
    $generator = app(\App\Services\Contract\UsersCsvExportContract::class);
    dd($generator->generate());
});

Route::get('google/redirect', [GoogleAuthController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('google/callback', [GoogleAuthController::class, 'handleGoogleCallback'])->name('google.callback');
