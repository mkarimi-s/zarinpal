<?php

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

Route::get('transactions', 'TransactionController@index')->name('index');
Route::group(['prefix' => 'transaction', 'as' => 'transaction.'], function (): void {
    Route::post('pos', 'TransactionController@store')->name('pos');
    Route::post('web', 'TransactionController@store')->name('web');
    Route::post('mobile', 'TransactionController@store')->name('mobile');
});
