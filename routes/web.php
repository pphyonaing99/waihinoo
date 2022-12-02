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
//Clear configurations:
			Route::get('/config-clear', function() {
				$status = Artisan::call('config:clear');
				return '<h1>Configurations cleared</h1>';
			});
			
//Clear cache:
			Route::get('/cache-clear', function() {
				$status = Artisan::call('cache:clear');
				return '<h1>Cache cleared</h1>';
			});

//Clear configuration cache:
			Route::get('/config-cache', function() {
				$status = Artisan::call('config:cache');
				return '<h1>Configurations cache cleared</h1>';
			});
//Route Clear:
			Route::get('/route-clear', function() {
				$status = Artisan::call('route:clear');
				return '<h1>Configurations cache cleared</h1>';
			});

Route::get('/','Web\VoucherController@voucherList')->name('voucherList');
Route::post('/voucherHistory','Web\VoucherController@voucherHistory')->name('voucherHistory');
Route::post('/getDailyProfit','Web\VoucherController@getDailyProfit')->name('getDailyProfit');
Route::post('/getMonthlyProfit','Web\VoucherController@getMonthlyProfit')->name('getMonthlyProfit');
Route::get('/getVoucherDetails/{voucher_id}','Web\VoucherController@voucher')->name('getVoucherDetails');
