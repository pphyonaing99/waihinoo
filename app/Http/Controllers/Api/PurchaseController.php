<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\apiBaseController;
use Illuminate\Http\Request;
use App\Purchase;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\Product;
use App\Accessory;
use App\Http\Controllers\Api\SupplierController;
use App\Supplier; 
 
class PurchaseController extends apiBaseController
{
    public function all(Request $request){
        $validator = Validator::make($request->all(), [
            'user_code' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('အချက်အလက် များ မှားယွင်း နေပါသည်။');
        }
    	
    	$purchases = Purchase::on($db_name)->where('branch_id', $branch_id)->get();
    	return $this->sendResponse('purchases', $purchases);

    }

    public function store(Request $request){
    	$validator = $this->validator($request);

        if ($validator->fails()) {
            return $this->sendError('အချက်အလက် များ မှားယွင်း နေပါသည်။');
        }
        
        $purchase_by = $request->purchase_by;
        
        $purchase_timetick = $request->purchase_date??time();
        $purchase_date = date("Y/m/d", $purchase_timetick);
        
        $total_amount = $request->total_amount;

    	$purchase = Purchase::create([
    	    
            'product_flag' => $request->product_flag,
            'product_id' => $request->product_id??null,
            'purchase_quantity' => $request->purchase_quantity,
            'supplier_id' => $request->supplier_id,
            'timetick' => time(),
            'purchase_type' => $request->purchase_type??null,
            'purchase_by' => $request->purchase_by,
            'purchase_date' => $purchase_date,
            'description' => $request->description == null? "":$request->description,
            'exchange_rate' => $request->exchange_rate,
            'amount' => $request->amount??0,
            'total_amount' => $total_amount??0,
            'currency_type' => $request->currency_type,
    	]);
    	
    	// add into payable amount if purchase type is credit
    	if($request->purchase_type == "credit"){

    	    $supplier = Supplier::find($request->supplier_id);

    	    if(empty($supplier)){
    	        return $this->sendError("Wrong Supplier");
    	    }

    	    $supplier->payable += (int)$supplier->payable+$total_amount;
    	    $supplier->save();

    	    $supplier_cont = new SupplierController;
    	    $purchase_request = new Request;
    	    $purchase_request->setMethod("POST");
    	    $purchase_request->request->add([
    	        "supplier_id" => $supplier->id,
    	        "name" => $supplier->name,
    	        "address" => $supplier->address,
    	        "contact" => $supplier->contact,
    	        "repayment_period" => $supplier->repayment_period
    	    ]);
    	    
    	    $supplier_payment_request = new Request;
    	    $supplier_payment_request->setMethod("POST");
    	    $supplier_payment_request->request->add([
    	        "supplier_id" => $supplier->id,
    	        "purchase_id" => $purchase->id,
    	        "total_credit_amount" => $total_amount,
    	        "purchase_timetick" => $purchase_timetick
    	    ]);
    	    
    	    $supplier_cont->createPaymentHistory($supplier_payment_request);
    	    $response = $supplier_cont->update($purchase_request);
    	}    	
                       
    	return $this->sendResponse('purchase', $purchase);

    }
    public function getPurchaseListWithCredit(Request $request){
        $validator = Validator::make($request->all(), [
            "supplier_id" => "required"
        ]);
        if ($validator->fails()) {
            return $this->sendError('အချက်အလက် များ မှားယွင်း နေပါသည်။');
        }

    	$purchases = Purchase::where("purchase_type", "credit")->where('supplier_id', '=', $request->supplier_id)->get();
        
        return $this->sendResponse('purchases', $purchases);
    }
    protected function validator(Request $request)
    {
        return Validator::make($request->all(), [
            'supplier_id' => 'required',
            'purchase_type' => 'required',
            'purchase_date' => 'required',
            'description' => 'required',
            'exchange_rate' => 'required',
            'amount' => 'required',
            'currency_type' => 'required'
        ]);
    }
}
