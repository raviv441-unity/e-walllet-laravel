<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('/login');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

//Wallet routes
Route::group(['middleware' => 'auth'], function(){
    Route::get('send-money', [App\Http\Controllers\WalletController::class, 'sendMoney'])->name('send-money');
    Route::get('transaction-history', [App\Http\Controllers\WalletController::class, 'transactionHistory'])->name('transaction-history');
    Route::get('list-users', [App\Http\Controllers\WalletController::class, 'listUsers'])->name('list-users');
    Route::get('user-details/{id}', [App\Http\Controllers\WalletController::class, 'userDetail'])->name('user-detail');

    Route::post('transfer-money/{id}',[App\Http\Controllers\WalletController::class, 'transferMoney'])->name('transfer-money');
});