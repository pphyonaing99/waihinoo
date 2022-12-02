<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\apiBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\CustomDiscount;
use App\Product;
use App\Accessory;

class DiscountController extends apiBaseController
{
    
    public function all(Request $request){
        
        $discounts = CustomDiscount::all();

        return $this->sendResponse('discounts', $discounts);
    }

    public function store(Request $request){
        
        $validator = Validator::make($request->all(), [
            "name" => "required",
        ]);

        if ($validator->fails()) {
            return $this->sendError('အချက်အလက် များ မှားယွင်း နေပါသည်။');
        }

        if ($request->hasfile('photo')) {

            $image = $request->file('topping_photo_path');
            $name = $image->getClientOriginalName();
            $image->move(public_path() . '/image/', $name);
            $image = $name;
        }
        
        $discount = CustomDiscount::create([
            "name" => $request->name,
            "discount_period_from" => $request->discount_period_from,
            "discount_period_to" => $request->discount_period_to,
            "unlimited_time_flag" => $request->unlimited_time_flag??2,
            "description" => $request->description,
            "photo" => $image??null,
            "discount_type_flag" => $request->discount_type_flag,
            "condition_type_flag" => $request->condition_type_flag,
            "discount_applied_flag" => $request->discount_applied_flag,
            "announce_customer_flag" => $request->announce_customer_flag??0,
        ]);

        //Discount for amount percent and product id
        if ($request->discount_type_flag == 3) {
            $discount->discount_type_flag = $request->discount_type_flag;
            $discount->discount_percent = $request->discount_percent;
            $discount->save();
        }elseif ($request->discount_type_flag == 1) {
            $discount->discount_type_flag = $request->discount_type_flag;
            $discount->discount_fixed_amount = $request->discount_fixed_amount;
            $discount->save();
        }elseif ($request->discount_type_flag == 2) {
            $discount->discount_type_flag = $request->discount_type_flag;
            $discount->discount_product_id = $request->discount_product_id;
            
            // $products = d);
            // return response()->json($products);
            
            foreach(explode(',',$request->discount_product_id) as $product_id){
                $accessory = Accessory::find($product_id);
                $accessory->custom_discount_id = $discount->id;
                $accessory->save();
            }
            
            $discount->save();
        }

        if ($request->discount_applied_flag == 1) { 
            $discount->discount_applied_flag = $request->discount_applied_flag;
            $discount->applied_type_id = $request->applied_type_id;
            $discount->save();
            
            //John Edited according to Ko Win Request (need to ask Zin Wah)
            $product = Product::find($request->applied_type_id);
            
            $product->custom_discount_flag = 1;
            
            $product->custom_discount_id = $discount->id;
            
            $product->save();
        }

        if ($request->condition_type_flag == 3) {
            $discount->condition_type_flag = $request->condition_type_flag;
            $discount->condition_amount = $request->condition_amount;
            $discount->save();
        }elseif ($request->condition_type_flag == 1) {
            $discount->condition_type_flag = $request->condition_type_flag;
            $discount->condition_range_from = $request->condition_range_from;
            $discount->condition_range_to = $request->condition_range_to;
            $discount->save();
        }elseif ($request->condition_type_flag == 2) {
            $discount->condition_type_flag = $request->condition_type_flag;
            $discount->condition_product_id = $request->condition_product_id;
            $discount->condition_product_qty = $request->condition_product_qty;
            $discount->save();
        }

        $discounts = CustomDiscount::find($discount->id);

        
        return $this->sendResponse('discounts', $discounts);
    }
    
    public function discountDetails(Request $request){

        $validator = Validator::make($request->all(), [
            "discount_id" => "required",
        ]);

        if ($validator->fails()) {
            return $this->sendError('အချက်အလက် များ မှားယွင်း နေပါသည်။');
        }

        $discount = CustomDiscount::find($request->discount_id);

        return $this->sendResponse('discount', $discount);

    }
}
