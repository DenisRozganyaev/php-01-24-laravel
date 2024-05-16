<?php

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

Route::get('/', \App\Http\Controllers\HomeController::class)->name('home');

Auth::routes();

Route::resource('products', \App\Http\Controllers\ProductsController::class)->only(['index', 'show']);
Route::resource('categories', \App\Http\Controllers\CategoriesController::class)->only(['index', 'show']);

Route::name('ajax.')->prefix('ajax')->group(function() {
   Route::group(['auth', 'role:admin|moderator'], function() {
       Route::post('products/{product}/image', \App\Http\Controllers\Ajax\Products\UploadImage::class)->name('products.image.upload');
       Route::delete('images/{image}', \App\Http\Controllers\Ajax\RemoveImageController::class)->name('image.remove');
   });

   Route::prefix('paypal')->name('paypal.')->group(function() {
      Route::post('order/create', [\App\Http\Controllers\Ajax\Payments\PaypalController::class, 'create'])->name('create'); // ajax.paypal.create
      Route::post('order/{vendorOrderId}/capture', [\App\Http\Controllers\Ajax\Payments\PaypalController::class, 'capture'])->name('capture'); // ajax.paypal.capture
   });
});

Route::name('admin.')->prefix('admin')->middleware('role:admin|moderator')->group(function() {
    Route::get('/', \App\Http\Controllers\Admin\DashboardController::class)->name('dashboard');
    Route::resource('categories', \App\Http\Controllers\Admin\CategoriesController::class)->except(['show']);
    Route::resource('products', \App\Http\Controllers\Admin\ProductsController::class)->except(['show']);
});

Route::name('cart.')->prefix('cart')->group(function() {
   Route::get('/', [\App\Http\Controllers\CartController::class, 'index'])->name('index');
   Route::post('{product}', [\App\Http\Controllers\CartController::class, 'add'])->name('add');
   Route::delete('/', [\App\Http\Controllers\CartController::class, 'remove'])->name('remove');
   Route::post('{product}/count', [\App\Http\Controllers\CartController::class, 'count'])->name('count');
});
Route::get('checkout', \App\Http\Controllers\CheckoutController::class)->name('checkout');
