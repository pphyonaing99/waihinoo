<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\apiBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Accessory;

class AccessoriesController extends apiBaseController
{
    public function all(Request $request){
        
        $accessories = Accessory::orderBy('id','desc')->get();

        return $this->sendResponse('accessories', $accessories);
    }

    public function store(Request $request){
        
        $validator = Validator::make($request->all(), [
            "name" => "required",
            "brand_id" => "required",
            "category_id" => "required",
            "supplier_id" => "required",
            "instock_qty" => "required",
            "reorder_qty" => "required",
            "serial_number" => "required",
            "purchase_price" => "required",
            "instock_qty" => "required",
            "purchase_currency" => "required",
        ]);
        
        if ($validator->fails()) {
            return $this->sendError('အချက်အလက် များ မှားယွင်း နေပါသည်။');
        }
        
        if ($request->hasfile('photo')) {

			$image = $request->file('photo');
			$name = $image->getClientOriginalName();
			$image->move(public_path() . '/image/', $name);
			$image = $name;
		}
        
        $accessory = Accessory::create([
            "name" => $request->name,
            "photo" => $image??null,
            "category_id" => $request->category_id,
            "brand_id" => $request->brand_id,
            "supplier_id" => $request->supplier_id,
            "instock_qty" => $request->instock_qty,
            "reorder_qty" => $request->reorder_qty,
            "serial_number" => $request->serial_number,
            "model_number" => $request->model_number,
            "color" => $request->color,
            "size" => $request->size,
            "purchase_price" => $request->purchase_price,
            "purchase_currency" => $request->purchase_currency,
            "sales_price" => $request->sales_price,
            "sales_currency" => $request->sales_currency,
            "exchange_rate" => $request->exchange_rate,
            "discount_flag" => $request->discount_flag??0,
            "foc_item_flag" => $request->foc_item_flag??0,
            "custom_discount_flag" => $request->custom_discount_flag??0,
            "specification_description" => $request->specification_description,
        ]);

        if ($request->discount_flag == 1) {
        	$accessory->discount_percent = $request->discount_percent;
        	$accessory->save();
        }

        if ($request->custom_discount_flag == 1) {
        	$accessory->custom_discount_id = $request->custom_discount_id;
        	$accessory->save();
        }
        
        return $this->sendResponse('accessory', $accessory);
    }

    public function update(Request $request){

        $validator = Validator::make($request->all(), [
            "accessory_id" => "required",
            "name" => "required",
            "brand_id" => "required",
            "category_id" => "required",
            "supplier_id" => "required",
            "instock_qty" => "required",
            "serial_number" => "required",
            "purchase_price" => "required",
            "instock_qty" => "required",
            "purchase_currency" => "required",
        ]);

        if ($validator->fails()) {
            return $this->sendError('အချက်အလက် များ မှားယွင်း နေပါသည်။');
        }

        $accessory = Accessory::find($request->accessory_id);

        if (empty($accessory)) {
            return $this->sendError('Accessory not found!');
        }

        if ($request->hasfile('photo')) {

            $image = $request->file('photo');
            $name = $image->getClientOriginalName();
            $image->move(public_path() . '/image/', $name);
            $image = $name;
            $accessory->photo = $image;
            $accessory->save();
        }

        $accessory->name = $request->name;
        $accessory->category_id = $request->category_id;
        $accessory->brand_id = $request->brand_id;
        $accessory->supplier_id = $request->supplier_id;
        $accessory->instock_qty = $request->instock_qty;
        $accessory->serial_number = $request->serial_number;
        $accessory->model_number = $request->model_number;
        $accessory->color > $request->color;
        $accessory->size = $request->size;
        $accessory->purchase_price = $request->purchase_price;
        $accessory->purchase_currency = $request->purchase_currency;
        $accessory->sales_price = $request->sales_price;
        $accessory->sales_currency = $request->sales_currency;
        $accessory->exchange_rate = $request->exchange_rate;
        $accessory->discount_flag = $request->discount_flag??0;
        $accessory->foc_item_flag = $request->foc_item_flag??0;
        $accessory->custom_discount_flag = $request->custom_discount_flag??0;
        $accessory->specification_description = $request->specification_description;

        if ($request->discount_flag == 1) {
            $accessory->discount_percent = $request->discount_percent;
            $accessory->save();
        }

        if ($request->custom_discount_flag == 1) {
            $accessory->custom_discount_id = $request->custom_discount_id;
            $accessory->save();
        }

        $accessory->save();

        return $this->sendResponse('accessory' , $accessory);

    }
    
    public function accessoryStockUpdate(Request $request) { 
        $validator = Validator::make($request->all(), [
            "accessory_id" => "required",
        ]);
        
        if ($validator->fails()) {
            return $this->sendError('အချက်အလက် များ မှားယွင်း နေပါသည်။');
        }
        
        $accessory = Accessory::find($request->accessory_id);
        
        if(empty($accessory)) {
            return $this->sendError('Accessory not found');
        }
        
        $accessory->instock_qty += $request->income_qty;
        $accessory->save();
        
        return $this->sendResponse('data',$accessory);
    }
}
