<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\apiBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Product;
use App\ProductWithImei;
use App\CustomDiscount;
use App\Defect;
use App\Accessory;
use App\ExchangeProduct;
use App\Supplier;

class ProductController extends apiBaseController
{
    public function all(Request $request){
        
        $products = Product::select('id','name','category_id','brand_id','supplier_id','model_number','purchase_price','purchase_currency','instock_qty','sales_currency','discount_flag','discount_percent','gift_flag','custom_discount_flag','custom_discount_id','specification_description','photo','series_flag','size','gift_item_id')->orderBy('id','desc')->get();

        return $this->sendResponse('products', $products);
        
    }
    
    public function exchangeProductList(Request $request) {
        $exchange_products = ExchangeProduct::orderBy('id','desc')
                    ->where('status',0)
                    ->get();
        
        foreach($exchange_products as $product) {
            $supplier = Supplier::find($product->supplier_id);
            
            if(!empty($supplier)) {
                $product['supplier_name'] = $supplier->name;
            }
        }
        
        return $this->sendResponse('data',$exchange_products);
    }
    
    public function exchangeProductStatus(Request $request) {
        
        $validator = Validator::make($request->all(), [
            "exchange_id" => "required",
        ]);
        
        if ($validator->fails()) {
            return $this->sendError('အချက်အလက် များ မှားယွင်း နေပါသည်။');
        }
        
        $exchange_products = ExchangeProduct::find($request->exchange_id);
        
        $exchange_products->status = 1;
        $exchange_products->save();
        
        return $this->sendResponse('data',$exchange_products);
    }
    
    public function exchangeProductDetail(Request $request) {
        
        $validator = Validator::make($request->all(), [
            "exchange_id" => "required",
        ]);
        
        if ($validator->fails()) {
            return $this->sendError('အချက်အလက် များ မှားယွင်း နေပါသည်။');
        }
        
        $exchange_products = ExchangeProduct::where('id',$request->exchange_id)
                    ->first();
                    
        $supplier = Supplier::find($product->supplier_id);
        
        if(!empty($supplier)) {
            $product['supplier_name'] = $supplier->name;
        }
        $exchange_products['supplier_name'] = $supplier->name;
        
        return $this->sendResponse('data',$exchange_products);
    }

    public function productDetail(Request $request){

        $validator = Validator::make($request->all(), [
            "product_id" => "required",
        ]);
        
        if ($validator->fails()) {
            return $this->sendError('အချက်အလက် များ မှားယွင်း နေပါသည်။');
        }

        $product_with_imeis = ProductWithImei::where('product_id',$request->product_id)->where('sold_flag',0)->get();

        foreach ($product_with_imeis as $value) {
            $product = Product::find($value->product_id);
            $value['product_name'] = $product->name;
            $value['instock_qty'] = $product->instock_qty;
        }

        return $this->sendResponse('products', $product_with_imeis);

    }

     public function store(Request $request){
         
        $validator = Validator::make($request->all(), [
            "name" => "required",
            "brand_id" => "required",
            "category_id" => "required",
            "supplier_id" => "required",
            "instock_qty" => "required",
            "reorder_qty" => "required",
            "purchase_currency" => "required",
        ]);
        
        if ($validator->fails()) {
            return $this->sendError('အချက်အလက် များ မှားယွင်း နေပါသည်။');
        }
        
        $product = Product::create([
            "name" => $request->name,
            "category_id" => $request->category_id,
            "brand_id" => $request->brand_id,
            "supplier_id" => $request->supplier_id,
            "instock_qty" => 0,
            "reorder_qty" => intval($request->reorder_qty),
            "imei_number" => "",
            "model_number" => $request->model_number,
            "size" => $request->size,
            "purchase_currency" => $request->purchase_currency,
            "sales_currency" => $request->sales_currency,
            "discount_flag" => $request->discount_flag??0,
            "gift_flag" => $request->gift_flag??0,
            "custom_discount_flag" => $request->custom_discount_flag??0,
            "specification_description" => $request->specification_description,
        ]);

        if ($request->discount_flag == 1) {
        	$product->discount_percent = $request->discount_percent;
        	$product->save();
        }

        if ($request->gift_flag == 1) {
        	$product->gift_item_id = $request->gift_item_id;
        	$product->save();
        }

        if ($request->custom_discount_flag == 1) {
        	$product->custom_discount_id = $request->custom_discount_id;
        	$product->save();
        }

        if ($request->hasfile('photo')) {

			$image = $request->file('photo');
			$name = $image->getClientOriginalName();
			$image->move(public_path() . '/image/', $name);
			$image = $name;
			$product->photo = $image;
			$product->save();
		}
        
        return $this->sendResponse('product', $product);
    }

    public function storeProductWithImei(Request $request){

        $validator = Validator::make($request->all(), [
            "product_id" => "required",
            "product_with_imei" => "required",
        ]);
        
        if ($validator->fails()) {
            return $this->sendError('အချက်အလက် များ မှားယွင်း နေပါသည်။');
        }
        
        if($this->checkExistImei($request->product_with_imei) > 0) {
            return $this->sendError('Imei number for product is already inserted');
        }

        foreach ($request->product_with_imei as $imei) {

            $product = Product::find($request['product_id']);

             $product_with_imei = ProductWithImei::create([
                'product_id' => $request['product_id'],
                'imei_number' => $imei['imei_number'],
                'color' => $imei['color'],
                'internal_storage' => $imei['internal_storage'],
                'ram' => $imei['ram'],
                'cpu' => $imei['cpu']??null,
                'camera' => $imei['camera'],
                'purchase_price' => $imei['purchase_price']??0,
                'sales_price' => $imei['sales_price']??0,
            ]);
            
            $product_with_imei->updated_at = $imei['updated_at'];
            $product->instock_qty += 1;
        
            $product->save();
            $product_with_imei->save();

        }
        
        $qty = count($request->product_with_imei);   
        
        $product->imei_number = $request->product_with_imei;

        $product_with_imei = [];

        $product_with_imeis = ProductWithImei::select('id','product_id','imei_number','color','internal_storage','ram','cpu','camera','purchase_price','sales_price')->where('product_id',$product->id)->get();

        foreach ($product_with_imeis as $imeis) {
            $imeis['product_name'] = $product->name;
            array_push($product_with_imei, $imeis);
        }

        return $this->sendResponse('product_with_imeis', $product_with_imei);

    }
    
    protected function checkExistImei($product_with_imei) {
        $count = 0;
        foreach($product_with_imei as $imei) {
            $product_with_imeis = ProductWithImei::where('imei_number',$imei['imei_number'])
                                    ->count();
                                    
            if($product_with_imeis) {
                $count += $product_with_imeis;
            }
        }
        return $count;
    }

    public function editSellingPrice(Request $request){

            $validator = Validator::make($request->all(),[
                "imei_number" => "required",
                "updated_at" => "required",
            ]);
            if($validator->fails()){
                return $this->sendError('အချက်အလက် များ မှားယွင်း နေပါသည်။');
            }

            $product_with_imei = ProductWithImei::where('imei_number',$request->imei_number)->first();

            if(empty($product_with_imei)){
                return $this->sendError('Product not found!');
            }

            $product_with_imei->imei_number = $request->imei_number;
            $product_with_imei->color = $request->color;
            $product_with_imei->internal_storage = $request->internal_storage;
            $product_with_imei->ram = $request->ram;
            $product_with_imei->cpu = $request->cpu;
            $product_with_imei->camera = $request->camera;
            $product_with_imei->purchase_price = $request->purchase_price;
            $product_with_imei->sales_price = $request->sales_price;
            $product_with_imei->updated_at = $request->updated_at;
            $product_with_imei->save();

            return $this->sendResponse('product_with_imei' , $product_with_imei);

    }

    public function searchWithImei(Request $request){

        $validator = Validator::make($request->all(), [
            "imei_number" => "required",
        ]);
        
        if ($validator->fails()) {
            return $this->sendError('အချက်အလက် များ မှားယွင်း နေပါသည်။');
        }

        $product_with_imei = ProductWithImei::where('imei_number',$request->imei_number)->first();

        $product = Product::find($product_with_imei->product_id);

        $product_with_imei['product_name'] = $product->name;

        return $this->sendResponse('product_with_imei',$product_with_imei);

    }
    
    public function editProduct(Request $request){
        
        $validator = Validator::make($request->all(),[
            "product_id" => "required",
            "name" => "required",
            "model_number" => "required",
            "category_id" => "required",
            "brand_id" => "required",
            "supplier_id" => "required",
        ]);
        
        if($validator->fails()){
            return $this->sendError('အချက်အလက် များ မှားယွင်း နေပါသည်။');
        }
        
        $product = Product::find($request->product_id);

        if (empty($product)) {
            return $this->sendError('Product not found');
        }
        
        $product->name = $request->name;
        $product->model_number = $request->model_number;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;
        $product->supplier_id = $request->supplier_id;
        $product->discount_flag = $request->discount_flag;
        $product->discount_percent = $request->discount_percent;
        $product->custom_discount_flag = $request->custom_discount_flag;
        $product->custom_discount_id = $request->custom_discount_id;
        $product->save();
			
		$product->imei_number = "";
		

        return $this->sendResponse('product',$product);
        
    }
    
    public function editGiftItem(Request $request){
        
        $validator = Validator::make($request->all(),[
            "product_id" => "required",
            "gift_flag" => "required",
        ]);
        
        if($validator->fails()){
            return $this->sendError('အချက်အလက် များ မှားယွင်း နေပါသည်။');
        }
        
        $product = Product::find($request->product_id);

        if (empty($product)) {
            return $this->sendError('Product not found');
        }
        
        $product->gift_flag = $request->gift_flag;
        
        $product->gift_item_id = $request->gift_item_id??null;
        
        $product->save();

        $product->imei_number = "";
        
        return $this->sendResponse('product',$product);
    }
    
    public function getDefectItemList(Request $request){
        
        $defect_items = Defect::all();
                    
        foreach($defect_items as $item) {
            $product = Product::find($item->product_id);
            $accessory = Accessory::find($item->accessory_id);
            
            $item['product_name'] = $product->name??null;
            $item['accessory_name'] = $accessory->name??null;
        }
                    
        return $this->sendResponse('defect_products',$defect_items);
    }
    public function storeDefectItem(Request $request){
        $validator = Validator::make($request->all(), [
            'qty' => 'required',
            'defect_date' => 'required',
        ]);
        if($validator->fails()){
            return $this->sendError('အချက်အလက် များ မှားယွင်း နေပါသည်။');
        }
        
        $product = ProductWithImei::where('product_id',$request->product_id)
                ->where('imei_number',$request->imei_number)
                ->first();
                
        $accessory = Accessory::find($request->accessory_id);
        
        $defect_item = Defect::create([
            'qty' => $request->qty,
            'product_id' => $request->product_id??null,
            'user_id' => $request->user_id,
            'comment' => $request->comment??"",
            'defect_date' => $request->defect_date,
            'imei_number' => $request->imei_number??null,
            'accessory_id' => $request->accessory_id??null,
            'product_flag' => $request->product_flag??0,
        ]);
        
        if($request->product_flag == 1){
            if(empty($product)) {
                return $this->sendError('Product not found');
            }
            $product->defect_flag = 1;
            $product->save();
        }else{
            if(empty($accessory)) {
                 return $this->sendError('Accessory not found');
            }
            // $accessory->instock_qty += 1;
            $accessory->save();
        }
        
        return $this->sendResponse('defect_products',$defect_item);
       
    }  
}
