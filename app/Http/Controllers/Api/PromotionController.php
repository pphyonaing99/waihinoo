<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\apiBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\CustomPromotion;

class PromotionController extends apiBaseController
{
    public function all(Request $request){
        
        $promotions = CustomPromotion::all();

        return $this->sendResponse('promotions', $promotions);
    }

    public function store(Request $request){
        
        $validator = Validator::make($request->all(), [
            "name" => "required",
            "promotion_period_from" => "required",
            "promotion_period_to" => "required",
            "description" => "required",
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
        
        $promotion = CustomPromotion::create([
            "name" => $request->name,
            "promotion_period_from" => $request->promotion_period_from,
            "promotion_period_to" => $request->promotion_period_to,
            "description" => $request->description,
            "photo" => $image??null,
            "condition" => $request->condition,
            "reward_flag" => $request->reward_flag,
            "discount_flag" => $request->discount_flag,
            "link_customer_flag" => $request->link_customer_flag??0,
            "announce_customer_flag" => $request->announce_customer_flag??0,
        ]);

        //Promotion for amount percent and product id
        if ($request->condition == 3) {
            $promotion->condition_amount = $request->condition_amount;
            $promotion->save();
        }elseif ($request->condition == 1) {
            $promotion->condition_product_id = $request->condition_product_id;
            $promotion->condition_product_qty = $request->condition_product_qty;

            $producut = Product::find($request->condition_product_id);
            $product->discount_promotion_id = $promotion->id;
            
            $product->save();
            $promotion->save();
        }

        if ($request->reward_flag == 3) {
            $promotion->cashback_amount = $request->cashback_amount;
            $promotion->save();
        }elseif ($request->reward_flag == 1) {
            if ($request->discount_flag == 0 ) {
                $promotion->custom_discount_id = $request->custom_discount_id;
                $promotion->save();
            }elseif ($request->discount_flag == 2 ) {
                $promotion->discount_percent = $request->discount_percent;
                $promotion->save();
            }
        }elseif ($request->reward_flag == 2) {
            $promotion->reward_product_id = $request->reward_product_id;
            $promotion->save();
        }

        $promotions = CustomPromotion::find($promotion->id);

        
        return $this->sendResponse('promotions', $promotions);
    }
    public function promotionDetails(Request $request){

        $validator = Validator::make($request->all(), [
            "promotion_id" => "required",
        ]);

        if ($validator->fails()) {
            return $this->sendError('အချက်အလက် များ မှားယွင်း နေပါသည်။');
        }

        $promotion = CustomPromotion::find($request->promotion_id);

        return $this->sendResponse('promotion', $promotion);

    }
}
