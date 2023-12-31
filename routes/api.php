<?php

use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Product;
use App\Events\ProductOutOfStock;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/products', [ProductController::class, 'index'])->name('products.index');

Route::get('/test-outofstock-event', function () {
    $product = Product::first();

    if ($product) {
        event(new ProductOutOfStock($product));
        return 'Event fired and email sent!';
    }

    return 'No product found to test with.';
});
