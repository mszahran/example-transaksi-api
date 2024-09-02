<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/', function () {
    return 'Hello Transaksi API';
});
Route::prefix('v1/')->group(function () {
    Route::post('register', [App\Http\Controllers\Authentication::class, 'register']);
    Route::post('login', [App\Http\Controllers\Authentication::class, 'login']);
    Route::post('refresh', [App\Http\Controllers\Authentication::class, 'refresh']);
    Route::post('logout', [App\Http\Controllers\Authentication::class, 'logout']);
    Route::group(['middleware' => ['jwt.verify']], function () {
        Route::prefix('customer/')->group(function () {
            Route::get('list', [App\Http\Controllers\Customer::class, "listAllCustomers"]);
            Route::get('detail/{idCustomer}', [App\Http\Controllers\Customer::class, "detailCustomer"]);
        });
        Route::prefix('barang/')->group(function () {
            Route::get('list', [App\Http\Controllers\Barang::class, "listBarang"]);
        });
        Route::prefix('transaksi/')->group(function () {
            Route::get('count-transaksi', [App\Http\Controllers\Transaksi::class, "countTransaksi"]);
            Route::get('list', [App\Http\Controllers\Transaksi::class, "listTransaksi"]);
            Route::get('list/order/{column}/{order}', [App\Http\Controllers\Transaksi::class, "orderListTransaksi"]);
            Route::get('no-transaksi/create', [App\Http\Controllers\Transaksi::class, "createNoTransaksi"]);
            Route::post('save', [App\Http\Controllers\Transaksi::class, "saveTransaksi"]);
            Route::delete('cancel/{kodeSales}', [App\Http\Controllers\Transaksi::class, "cancelTransaksi"]);
            Route::prefix('barang-transaksi/')->group(function () {
                Route::post('create', [App\Http\Controllers\Transaksi::class, "createBarangTransaksi"]);
                Route::put('update', [App\Http\Controllers\Transaksi::class, "updateBarangTransaksi"]);
                Route::get('list/{kodeTransaksi}', [App\Http\Controllers\Transaksi::class, "listBarangTransaksi"]);
                Route::get('detail/{idSalesDet}', [App\Http\Controllers\Transaksi::class, "detailBarangTransaksi"]);
                Route::delete('delete/{idSalesDet}', [App\Http\Controllers\Transaksi::class, "deleteBarangTransaksi"]);
            });
        });
    });
});
