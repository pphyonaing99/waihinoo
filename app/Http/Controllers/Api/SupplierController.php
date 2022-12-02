<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\apiBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Supplier;
use App\User;
use App\SupplierPaymentHistory;
use App\SupplierRepaymentHistory;
use App\SupplierCashback;
use Illuminate\Support\Str;

class SupplierController extends apiBaseController
{

	public function all(Request $request){
        
        $suppliers = Supplier::all();

        return $this->sendResponse('suppliers', $suppliers);
    }

    public function store(Request $request){
        
        $validator = Validator::make($request->all(), [
            "name" => "required",
            "address" => "required",
            "phone" => "required",
            "email" => "required",
            "brand_id" => "required",
            "credit_amount" => "required",
            "repayment_period" => "required",
            "repayment_date" => "required",
        ]);

        if ($validator->fails()) {

            return $this->sendError('အချက်အလက် များ မှားယွင်း နေပါသည်။');
            
        }
        
        $supplier = Supplier::where('email',$request->email)->first();
        $user = User::where('email',$request->email)->first();
        
        if(!empty($supplier) || !empty($user)){
            return $this->sendError('Your Email address already exist!');
        }
        
        $supplier = Supplier::create([
            "name" => $request->name,
            "address" => $request->address,
            "phone" => $request->phone,
            "brand_id" => $request->brand_id,
            "email" => $request->email,
            "credit_amount" => $request->credit_amount,
            "credit_limit" => $request->credit_limit??0,
            "repayment_period" => $request->repayment_period,
            "repayment_date" => $request->repayment_date,
        ]);

        $supplier_code = $this->generateCode('S',$supplier->id);
        $supplier->supplier_code = $supplier_code;
        $supplier->save();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => \Hash::make($request->password),
            'remember_token' => Str::random(60),
        ]);


        $supplier->user_id = $user->id;
        $supplier->save();
        $supplier['token'] =  $this->get_user_token($user,"Personal Access Token");

        if ($request->has('role')) {
            $user->assignRole($request->role);
            $user->save();
        }
        
        return $this->sendResponse('supplier', $supplier);
    }

    public function supplierCashbackList(Request $request){

        $cashbacks = SupplierCashback::all();

        foreach ($cashbacks as $cashback) {
            $supplier = Supplier::find($cashback->supplier_id);
            $cashback['supplier_name'] = $supplier->name;
        }

        return $this->sendResponse('cashback' , $cashbacks);

    }

    public function supplierCashbackDetails(Request $request){

        $validator = Validator::make($request->all(), [
            "supplier_id" => "required",
        ]);

        if ($validator->fails()) {
            return $this->sendError('အချက်အလက် များ မှားယွင်း နေပါသည်။');
        }

        $supplier_cashback = SupplierCashback::where('supplier_id',$request->supplier_id)->get();

        if(empty($supplier_cashback)){
            return $this->sendError('Cashback not found!');
        }

        return $this->sendResponse('supplier_cashback',$supplier_cashback);

    }

    public function supplierCashback(Request $request){

        $validator = Validator::make($request->all(), [
            "supplier_cashback_id" => "required",
            "supplier_id" => "required",
        ]);

        if ($validator->fails()) {
            return $this->sendError('အချက်အလက် များ မှားယွင်း နေပါသည်။');
        }

        $supplier = Supplier::find($request->supplier_id);
        $supplier_cashback = SupplierCashback::find($request->supplier_cashback_id);

        if (empty($supplier)) {
            return $this->sendError('Supplier not found');
        }
        if (empty($supplier_cashback)) {
            return $this->sendError('Cashback not found');
        }

        // $cashback = SupplierCashback::create([
        //     'voucher_number' => $request->voucher_number,
        //     'supplier_id' => $request->supplier_id,
        //     'cashback' => $request->cashback,
        //     'date' => date('Y-m-d'),
        //     'product_id' => $request->product_id,
        //     'item_flag' => $request->item_flag??0,
        // ]);

        $supplier->cashback += $supplier_cashback->cashback;
        $supplier->save();

        return $this->sendResponse('cashback' , $cashback);

    }
    
    public function createPaymentHistory(Request $request){
        
        $supplier = Supplier::find($request->supplier_id);
        
        SupplierPaymentHistory::create([
            "supplier_id" => $supplier->id,
            "purchase_id" => $request->purchase_id,
            "total_credit_amount" => $request->total_credit_amount,
            "remaining_amount" => $request->total_credit_amount,
            "payment_due_date" => $this->calculatePaymentDueDate($supplier->repayment_period, $request->purchase_timetick)
        ]);
    }
    
    public function getList(Request $request){
        
       $suppliers = Supplier::all();

       return $this->sendResponse('suppliers', $suppliers);   
    }
    
    public function update(Request $request){
        $validator = Validator::make($request->all(), [
            "supplier_id" => "required",
            "name" => "required",
            "address" => "required",
            "phone" => "required",
        ]);
        
        if ($validator->fails()) {
            return $this->sendError('အချက်အလက် များ မှားယွင်း နေပါသည်။');
        }
        
        $supplier_id = $request->supplier_id;
        $name = $request->name;
        $address = $request->address;
        $phone = $request->phone;
        $brand = $request->brand_id;            //New Update field. John
        //$repayment_period = $request->repayment_period;   John Comment Pate For Zin Wah Request.

        $supplier = Supplier::find($supplier_id);

        if(empty($supplier)){
            return $this->sendError('Supplier not found');
        }
        
        $supplier->name = $name;
        $supplier->address = $address;
        $supplier->phone = $phone;
        $supplier->brand_id = $brand;
        //$supplier->repayment_period = $repayment_period;
        $supplier->save();
        return $this->sendResponse('supplier', $supplier);
        
    }
    
    public function getPaymentHistory(Request $request){
                
        $validator = Validator::make($request->all(), [
            "supplier_id" => "required",
        ]);

         if ($validator->fails()) {
            return $this->sendError('အချက်အလက် များ မှားယွင်း နေပါသည်။');
        }

        $payments = SupplierPaymentHistory::where('supplier_id',$request->supplier_id)->get();

        return $this->sendResponse("payment_history", $payments);
    }
    
    public function getRepaymentHistory(Request $request){
              
        $validator = Validator::make($request->all(), [
            "supplier_id" => "required",
        ]);

         if ($validator->fails()) {
            return $this->sendError('အချက်အလက် များ မှားယွင်း နေပါသည်။');
        }

        $repayments = SupplierRePaymentHistory::where('supplier_id',$request->supplier_id)->get();
        return $this->sendResponse("repayment_history", $repayments);
    }
    
    public function storeRepaymentHistory(Request $request){

        $validator = Validator::make($request->all(), [
            "supplier_id" => "required",
            "supplier_payment_history_id" => "required",
            "paid_amount" => "required",
            "paid_timetick" => "required" 
        ]);

         if ($validator->fails()) {
            return $this->sendError('အချက်အလက် များ မှားယွင်း နေပါသည်။');
        }

        $payment = SupplierPaymentHistory::where("id", "=", $request->supplier_payment_history_id)->first();
        if(empty($payment)){
            return $this->sendError("Payment not found");
        }
        
        if($payment->remaining_amount < $request->paid_amount){
            return $this->sendError("Paid amount cannot be more than the remaining credit amount");
        }
        $repayment = SupplierRepaymentHistory::create([
            "supplier_id" => $payment->supplier_id,
            "supplier_payment_history_id" => $request->supplier_payment_history_id,
            "paid_amount" => $request->paid_amount,
            "paid_timetick" => $request->paid_timetick
            ]);
        $payment->total_paid_amount = $payment->total_paid_amount + (int)$request->paid_amount;
        $payment->remaining_amount = $payment->remaining_amount - (int)$request->paid_amount;
        $payment->save();
        
        return $this->sendResponse("repayment_history", $repayment);
    }
}