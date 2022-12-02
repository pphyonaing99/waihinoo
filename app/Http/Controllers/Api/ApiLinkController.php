<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiLinkController extends Controller
{
    public function index(){

       
        return response()->json([

            "login" => "login",

            "categories_list" => "categories",
            "categories_store" => "categories/store",

            "brand_list" => "brands",
            "brand_store" => "brands/store",

            "supplier_list" => "suppliers",
            "supplier_store" => "suppliers/store",
            "supplier_payment_history_list" => "suppliers/getPaymentHistory",
            "supplier_payment_history_store" => "suppliers/storeRepaymentHistory",
            "supplier_repayment_history_list" => "suppliers/getRepaymentHistory",

            "purchase_list" => "purchases",

            "product_list" => "products",
            "product_store" => "products/store",
            "store_product_with_imei" => "products/storeProductWithImei",
            "search_product_with_imei" => "products/searchWithImei",
            "purchase_store" => "purchases/store",

            "accessories_list" => "accessories",
            "accessories_store" => "accessories/store",

            "discounts_list" => "discounts",
            "discounts_store" => "discounts/store",
            "discounts_details" => "discounts/discountDetails",

            "promotions_list" => "promotions",
            "promotions_store" => "promotions/store",
            "promotions_details" => "promotions/promotionDetails",

            "customers_list" => "customers",
            "customers_store" => "customers/store",
            "customer_credit_list" => "customers/customerCredit",
            "customer_credit_details" => "customers/customerCreditDetail",
            "customer_repayment_list" => "customers/getCustomerRepaymentLog",

            "vouchers_list" => "vouchers",
            "voucher_History" => "vouchers/voucherHistory",
            "voucher_Detail" => "vouchers/voucherDetail",
            "search_with_voucher_number" => "vouchers/searchWithVoucherNo",

            "all_sales" => "vouchers/profit",
            "monthly_sales" => "vouchers/getMonthlySales",
            "vouchers_store" => "vouchers/store",

            "employees_list" => "employees",
            "employees_store" => "employees/store",

            "installments_list" => "installments",
            "installments_store" => "installments/store",
            "aeon_payment_list" => "installments/getAeonPaymentList",
            "aeon_payment_history" => "installments/getAeonPaymentHistory",

            
        ]);
    }
}
