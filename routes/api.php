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

Route::get('/apilinks', function(Request $request){
    \Log::info( json_encode($request->all()));
    $obj = new App\Http\Controllers\api\ApiLinkController;
    return $obj->index();
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login', 'Api\LoginController@login');

Route::group(['middleware' => 'auth:api'], function(){

	//Category
	Route::post('/categories', 'Api\CategoryController@all');
	Route::post('/categories/store', 'Api\CategoryController@store');
	Route::post('/categories/update', 'Api\CategoryController@update');

	//Brand
	Route::post('/brands', 'Api\BrandController@all');
	Route::post('/brands/store', 'Api\BrandController@store');
	Route::post('/brands/update', 'Api\BrandController@update');

	//Supplier
	Route::post('/suppliers', 'Api\SupplierController@all');
	Route::post('/suppliers/store', 'Api\SupplierController@store');
	Route::post('/suppliers/update', 'Api\SupplierController@update');
	Route::post('/suppliers/getPaymentHistory', 'Api\SupplierController@getPaymentHistory');
	Route::post('/suppliers/storeRepaymentHistory', 'Api\SupplierController@storeRepaymentHistory');
	Route::post('/suppliers/getRepaymentHistory', 'Api\SupplierController@getRepaymentHistory');
	Route::post('/suppliers/supplierCashback', 'Api\SupplierController@supplierCashback');
	Route::post('/suppliers/supplierCashbackDetails', 'Api\SupplierController@supplierCashbackDetails');
    Route::post('/suppliers/supplierCashbackList', 'Api\SupplierController@supplierCashbackList');

	//Supplier Purchase
	Route::post('/purchases', 'Api\PurchaseController@all');
	Route::post('/purchases/store', 'Api\PurchaseController@store');

	//Product
	Route::post('/products', 'Api\ProductController@all');
	Route::post('/products/store', 'Api\ProductController@store');
	Route::post('/products/defect', 'Api\ProductController@getDefectItemList');
	Route::post('/products/store-defect', 'Api\ProductController@storeDefectItem');
	Route::post('/products/storeProductWithImei', 'Api\ProductController@storeProductWithImei');
	Route::post('/products/productDetail', 'Api\ProductController@productDetail');
	Route::post('/products/searchWithImei', 'Api\ProductController@searchWithImei');
	Route::post('/products/editSellingPrice', 'Api\ProductController@editSellingPrice');
	Route::post('/products/editProduct', 'Api\ProductController@editProduct');
	Route::post('/products/editGiftItem', 'Api\ProductController@editGiftItem');
	Route::post('/products/exchange-list', 'Api\ProductController@exchangeProductList');
	Route::post('/products/exchange-detail', 'Api\ProductController@exchangeProductDetail');
	Route::post('/products/exchange-status', 'Api\ProductController@exchangeProductStatus');

	//Accessories
	Route::post('/accessories', 'Api\AccessoriesController@all');
	Route::post('/accessories/store', 'Api\AccessoriesController@store');
	Route::post('/accessories/update', 'Api\AccessoriesController@update');
	Route::post('/accessories/stock-update', 'Api\AccessoriesController@accessoryStockUpdate');

	//Discount
	Route::post('/discounts', 'Api\DiscountController@all');
	Route::post('/discounts/store', 'Api\DiscountController@store');
	Route::post('/discounts/discountDetails', 'Api\DiscountController@discountDetails');

	//Promotion
	Route::post('/promotions', 'Api\PromotionController@all');
	Route::post('/promotions/store', 'Api\PromotionController@store');
	Route::post('/promotions/promotionDetails', 'Api\PromotionController@promotionDetails');

	//Customer
	Route::post('/customers', 'Api\CustomerController@all');
	Route::post('/customers/store', 'Api\CustomerController@store');
	Route::post('/customers/update', 'Api\CustomerController@update');
	Route::post('/customers/customerCredit', 'Api\CustomerController@checkCustomerCredit');
	Route::post('/customers/customerCreditDetail', 'Api\CustomerController@checkCustomerCreditDetails');
	Route::post('/customers/storeCustomerRepaymentLog', 'Api\CustomerController@storeCustomerRepaymentLog');
	Route::post('/customers/getCustomerRepaymentLog', 'Api\CustomerController@getCustomerRepaymentLog');

	//Voucher
	Route::post('/vouchers', 'Api\VoucherController@all');
	Route::post('/vouchers/store', 'Api\VoucherController@store');
	Route::post('/vouchers/storeWithCashBack', 'Api\VoucherController@storeWithCashBack');
	Route::post('/vouchers/sale-return', 'Api\VoucherController@saleReturn');
	Route::post('/vouchers/voucherHistory', 'Api\VoucherController@voucherHistory');
	Route::post('/vouchers/searchWithVoucherNo', 'Api\VoucherController@searchWithVoucherNo');
	Route::post('/vouchers/voucherDetail', 'Api\VoucherController@voucherDetail');
	Route::post('/vouchers/profit', 'Api\VoucherController@profit');
	Route::post('/vouchers/getMonthlySales', 'Api\VoucherController@getMonthlySales');

	//Employee
	Route::post('/employees/store', 'Api\EmployeeController@store');
	Route::post('/employees', 'Api\EmployeeController@all');
	Route::post('/employees/update', 'Api\EmployeeController@update');

	

	//Aeon and other payment
	Route::post('/installments', 'Api\PaymentController@all');
	Route::post('/installments/store', 'Api\PaymentController@store');
	Route::post('/installments/getAeonPaymentList', 'Api\PaymentController@getAeonPaymentList');
	Route::post('/installments/getAeonPaymentHistory', 'Api\PaymentController@getAeonPaymentHistory');
	Route::post('/installments/getAeonPaymentPlan', 'Api\PaymentController@getAeonPaymentPlan');

});



