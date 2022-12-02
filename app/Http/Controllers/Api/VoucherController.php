<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\apiBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Voucher;
use App\ProductProfit;
use App\AccessoryProfit;
use App\Accessory;
use App\Product;
use App\CustomerPaymentLog;
use App\Customer;
use App\ExchangeProduct;
use App\ProductWithImei;
use App\SupplierCashback;
use App\Helpers\Helpers;
use Illuminate\Support\Facades\DB;
use DateTime;

class VoucherController extends apiBaseController
{

    public function profit(Request $request){

        $month = date('m');

        $total_sales = DB::table('vouchers')->select(DB::raw('COALESCE(SUM(voucher_grand_total)) as total'))->get();
        $monthly = DB::table('vouchers')->select(DB::raw('COALESCE(SUM(voucher_grand_total)) as total'))->whereMonth('date',$month)->get();
        $total_sales_with_credit = DB::table('vouchers')->select(DB::raw('COALESCE(SUM(credit)) as total'))->get();
        $product_sales = DB::table('product_profits')->select(DB::raw('COALESCE(SUM(total_amount)) as total'))->get();
        $accessory_sales = DB::table('accessory_profits')->select(DB::raw('COALESCE(SUM(total_amount)) as total'))->get();

        return response()->json([

            'total_sales' => getIntValue($total_sales),
            'total_sales_with_credit' => getIntValue($total_sales_with_credit),
            'monthly_sales' => getIntValue($monthly),
            'product_sales' => getIntValue($product_sales),
            'accessory_sales' => getIntValue($accessory_sales),
            'success' => true,
            'message' => "successful"

        ]);

    }

    public function getMonthlySales(Request $request){

        $monthly = date('m',strtotime($request->month));

        $monthly_sales = DB::table('vouchers')->select(DB::raw('COALESCE(SUM(voucher_grand_total)) as total'))->whereMonth('date',$monthly)->get();

        $monthly_phone_sales = DB::table('product_profits')->select(DB::raw('COALESCE(SUM(total_amount)) as total'))->whereMonth('date',$monthly)->get();

        $monthly_accessory_sales = DB::table('accessory_profits')->select(DB::raw('COALESCE(SUM(total_amount)) as total'))->whereMonth('date',$monthly)->get();

        return response()->json([

            'monthly_sales' => getIntValue($monthly_sales),
            'monthly_phone_sales' => getIntValue($monthly_phone_sales),
            'monthly_accessory_sales' => getIntValue($monthly_accessory_sales),
            'success' => true,
            'message' => "successful"

        ]);
    }
    
    public function all(Request $request){

        $vouchers = Voucher::all();

        return $this->sendResponse('vouchers',$vouchers);
    }

    public function store(Request $request){
        
        $now = new DateTime('Asia/Yangon');

		$toady_date = $now->format('Y-m-d');

        $validator = Validator::make($request->all(), [
            "user_id" => "required",
            "sold_by" => "required",
        ]);
        
        if ($validator->fails()) {

            return $this->sendError('အချက်အလက် များ မှားယွင်း နေပါသည်။');

        }

        $voucher = Voucher::create([
            'voucher_number' => time(),
            'customer_id' => $request->customer_id??null,
            'user_id' => $request->user_id,
            'sold_by' => $request->sold_by,
            'item_list' => json_encode($request->item_list)??null,
            'accessory_list' => json_encode($request->accessory_list)??null,
            'total_amount' => $request->total_amount,
            'tax' => $request->tax,
            'total_discount' => $request->total_discount,
            'voucher_grand_total' => $request->voucher_grand_total,
            'total_quantity' => $request->total_quantity,
            'date' => $toady_date,
            'payment_type' => $request->payment_type,
        ]);
        
        if ( $request->cashback_flag == 1 ) {
            $cashback = SupplierCashback::create([
                'voucher_number' => $voucher->voucher_number,
                'supplier_id' => $request->supplier_id??null,
                'cashback' => $request->cashback,
                'date' => date('Y-m-d'),
                'product_id' => $request->product_id,
                'item_flag' => $request->item_flag??0,
            ]);
        }

        $payment_type = $request->payment_type;
        $voucher_grand_total = $request->voucher_grand_total;

        if ($request->customer_id != null) {
            $customer = Customer::find($request->customer_id);
            
            if(empty($customer)){
                return $this->sendError('Customer not found!');
            }
            
            $total_amount = intval($voucher_grand_total) + intval($customer->credit_balance);
        }

        
        if ($request->item_list != null) {
            foreach ($request->item_list as $voucher_data) {

                $product = Product::find($voucher_data['product_id']);
                
                if(empty($product)){
                    return $this->sendError('Product not found!');
                }
                
                if ( $product->instock_qty < $voucher_data['order_qty'] ) {
                    return $this->sendError('Out of Stock');
                }
                
                $product->instock_qty -= $voucher_data['order_qty'];
                
                $product_with_imei = ProductWithImei::where('imei_number',$voucher_data['imei_number'])
                            ->where('sold_flag',0)->first();
                
                if(!empty($product_with_imei)){
                    $product_with_imei->sold_flag = 1;
                    $product_with_imei->save();
                }else{
                     return $this->sendError('Your Imei Number is invalid or Already sold out');
                }
                
                $product->save();
                
                $product_profit = ProductProfit::where('product_id',$voucher_data['product_id'])
                        ->where('color',$voucher_data['color'])
                        ->where('date',$toady_date)
                        ->first();
                    

        //Insert into Profit if customer buying cashdown and insert to profit if customer credit limit is more than voucher total
                if ($payment_type == "cashdown") {
                    $total = $voucher_data['selling_price']*$voucher_data['order_qty'];
                    if($request->total_discount > 0){
                        $total -= $request->total_discount;
                    }
                    if (empty($product_profit)) {
                        ProductProfit::create([
                            'voucher_id' => $voucher->id,
                            'product_id' => $voucher_data['product_id'],
                            'color' => $voucher_data['color'],
                            'imei_number' => $voucher_data['imei_number'],
                            'total_amount' => $total,
                            'date' => $toady_date,
                        ]);
                    }else{
                        $product_profit->total_amount += $total;
                        $product_profit->save();
                    }
                }elseif ( $payment_type == "credit" ) {
                    if ( $total_amount > $customer->credit_limit ) {
                        $credit_balance = $customer->credit_limit;
                        $for_cash = $total_amount - $customer->credit_limit;

                        if (empty($accessory_profit)) {
                            AccessoryProfit::create([
                                'voucher_id' => $voucher->id,
                                'accessory_id' => $accessory_list['accessory_id'],
                                'color' => $accessory_list['color'],
                                'total_amount' => $for_cash,
                                'date' => date('Y-m-d'),
                            ]);
                        }else{
                            $accessory_profit->total_amount += $for_cash;
                            $accessory_profit->save();
                        }
                    }
                }
            }
        }   

        //insert to accessory list if access is not null
        if ($request->accessory_list != null) {
            foreach ($request->accessory_list as $accessory_list) {
    
                
                    $accessory = Accessory::find($accessory_list['accessory_id']);

                    if(empty($accessory)){
                        return $this->sendError('Accessory not found!');
                     }

                     $instock_qty = $accessory->instock_qty;  
                
                    if ( $instock_qty < 1 && $instock_qty < $accessory_list['order_qty'] ) {
                        return $this->sendError('Out of Stock');
                     }
                    
                     $accessory->instock_qty -= $accessory_list['order_qty'];
                     $accessory->save();
                     
                     if($accessory_list['selling_price'] != 0){

                        $accessory_profit = AccessoryProfit::where('accessory_id',$accessory_list['accessory_id'])
                                            ->where('color',$accessory_list['color'])
                                            ->where('date',$toady_date)
                                            ->first();

                        //Insert into Accessory Profit if customer buying cashdown and insert to profit if customer credit limit is more than voucher total
                        if ($payment_type == "cashdown") {
                            if (empty($accessory_profit)) {
                                AccessoryProfit::create([
                                    'voucher_id' => $voucher->id,
                                    'accessory_id' => $accessory_list['accessory_id'],
                                    'color' => $accessory_list['color'],
                                    'total_amount' => $accessory_list['selling_price']*$accessory_list['order_qty'],
                                    'date' => $toady_date,
                                ]);
                            }else{
                                $accessory_profit->total_amount += $accessory_list['selling_price'] * $accessory_list['order_qty'];
                                $accessory_profit->save();
                            }

                        }elseif ( $payment_type == "credit" ) {
                            if ( $total_amount > $customer->credit_limit ) {
                                $credit_balance = $customer->credit_limit;
                                $for_cash = $total_amount - $customer->credit_limit;

                                if (empty($accessory_profit)) {
                                    AccessoryProfit::create([
                                        'voucher_id' => $voucher->id,
                                        'accessory_id' => $accessory_list['accessory_id'],
                                        'color' => $accessory_list['color'],
                                        'total_amount' => $for_cash,
                                        'date' => $toady_date,
                                    ]);
                                }else{
                                    $accessory_profit->total_amount += $for_cash;
                                    $accessory_profit->save();
                                }
                            }
                        }
                    }    
            }
        }

        //store Customer Payment Log if voucher is with credit 
        if($payment_type == "credit"){

            $credit_balance = $customer->credit_balance;
            if($total_amount < $customer->credit_limit){
                $credit_balance = $total_amount;

                $customer_payment_log = CustomerPaymentLog::create([
                    "voucher_id" => $voucher->id,
                    "customer_id" => $customer->id,
                    "total_credit_amount" => $total_amount,
                    "total_paid_amount" => 0,
                    "remaining_amount" => $total_amount,
                    "payment_due_date" => $this->calculatePaymentDueDate($customer->allow_credit_period, time()),
                ]);
                $voucher->credit += $voucher_grand_total;
                $voucher->save();

            }else{
                $credit_balance = $customer->credit_limit;

                $customer_payment_log = CustomerPaymentLog::create([
                    "voucher_id" => $voucher->id,
                    "customer_id" => $customer->id,
                    "total_credit_amount" => $credit_balance,
                    "total_paid_amount" => 0,
                    "remaining_amount" => $credit_balance,
                    "payment_due_date" => $this->calculatePaymentDueDate($customer->allow_credit_period, time()),
                ]);

                $voucher->credit += $credit_balance;
                $voucher->save();

            }

            $customer->save();
            
        }
        
        $voucher_data = Voucher::find($voucher->id);
        return $this->sendResponse('voucher_data' , 'Successfully Sold');
    }
    
    public function storeWithCashBack(Request $request){
        
        $now = new DateTime('Asia/Yangon');

		$toady_date = $now->format('Y-m-d');

        $validator = Validator::make($request->all(), [
            "user_id" => "required",
            "sold_by" => "required",
        ]);
        
        if ($validator->fails()) {

            return $this->sendError('အချက်အလက် များ မှားယွင်း နေပါသည်။');

        }

        $voucher = Voucher::create([
            'voucher_number' => time(),
            'customer_id' => $request->customer_id??null,
            'user_id' => $request->user_id,
            'sold_by' => $request->sold_by,
            'item_list' => json_encode($request->item_list)??null,
            'accessory_list' => json_encode($request->accessory_list)??null,
            'total_amount' => $request->total_amount,
            'tax' => $request->tax,
            'total_discount' => $request->total_discount,
            'voucher_grand_total' => $request->voucher_grand_total,
            'total_quantity' => $request->total_quantity,
            'date' => $toady_date,
            'payment_type' => $request->payment_type,
            'cashback_flag' => $request->cashback_flag,
            'cashback_type' => $request->cashback_type,
            'cashback_amount'=> $request->cashback,
        ]);
        
        if ( $request->cashback_flag == 1 ) {
            $cashback = SupplierCashback::create([
                'voucher_number' => $voucher->voucher_number,
                'supplier_id' => $request->supplier_id??null,
                'cashback' => $request->cashback,
                'date' => date('Y-m-d'),
                'product_id' => $request->product_id,
                //'item_flag' => $request->item_flag??0,
                'item_flag' => $request->cashback_type,
            ]);
        }

        $payment_type = $request->payment_type;
        $voucher_grand_total = $request->voucher_grand_total;

        if ($request->customer_id != null) {
            $customer = Customer::find($request->customer_id);
            
            if(empty($customer)){
                return $this->sendError('Customer not found!');
            }
            
            $total_amount = intval($voucher_grand_total) + intval($customer->credit_balance);
        }

        
        if ($request->item_list != null) {
            foreach ($request->item_list as $voucher_data) {

                $product = Product::find($voucher_data['product_id']);
                
                if(empty($product)){
                    return $this->sendError('Product not found!');
                }
                
                if ( $product->instock_qty < $voucher_data['order_qty'] ) {
                    return $this->sendError('Out of Stock');
                }
                
                $product->instock_qty -= $voucher_data['order_qty'];
                
                $product_with_imei = ProductWithImei::where('imei_number',$voucher_data['imei_number'])
                            ->where('sold_flag',0)->first();
                
                if(!empty($product_with_imei)){
                    $product_with_imei->sold_flag = 1;
                    $product_with_imei->save();
                }else{
                     return $this->sendError('Your Imei Number is invalid or Already sold out');
                }
                
                $product->save();
                
                $product_profit = ProductProfit::where('product_id',$voucher_data['product_id'])
                        ->where('color',$voucher_data['color'])
                        ->where('date',$toady_date)
                        ->first();
                    
        
        //Insert into Profit if customer buying cashdown and insert to profit if customer credit limit is more than voucher total
        
                $total_phone_profits = 0;
                $total_acc_profits = 0;
                if ($payment_type == "cashdown") {
                    $total_phone_profits = $voucher_data['selling_price'] * $voucher_data['order_qty'];
                    
                    //if cashback for disocunt is phone
                    if($request->cashback_flag == 1 && $request->cashback_type == 1 && $request->supplier_id == 10){
                        $total_phone_profits -= $request->cashback;
                    }
                    
                    $total = $voucher_data['selling_price']*$voucher_data['order_qty'];
                    if($request->total_discount > 0){
                        $total_phone_profits -= $request->total_discount;
                    }
                    
                    if (empty($product_profit)) {
                        ProductProfit::create([
                            'voucher_id' => $voucher->id,
                            'product_id' => $voucher_data['product_id'],
                            'color' => $voucher_data['color'],
                            'imei_number' => $voucher_data['imei_number'],
                            'total_amount' => $total_phone_profits,
                            'date' => $toady_date,
                        ]);
                    }else{
                        $product_profit->total_amount += $total_phone_profits;
                        $product_profit->save();
                    }
                }elseif ( $payment_type == "credit" ) {
                    
                    if ( $total_amount > $customer->credit_limit ) {
                        $credit_balance = $customer->credit_limit;
                        $for_cash = $total_amount - $customer->credit_limit;
                        

                        if (empty($accessory_profit)) {
                            AccessoryProfit::create([
                                'voucher_id' => $voucher->id,
                                'accessory_id' => $accessory_list['accessory_id'],
                                'color' => $accessory_list['color'],
                                'total_amount' => $for_cash,
                                'date' => date('Y-m-d'),
                            ]);
                        }else{
                            $accessory_profit->total_amount += $for_cash;
                            $accessory_profit->save();
                        }
                    }
                }
            }
        }   

        //insert to accessory list if access is not null
        if ($request->accessory_list != null) {
            $cashback_even = round($request->cashback/$request->total_quantity,2);
            $total_cashback_reduce = 0;
            $total_item = 0;
            foreach ($request->accessory_list as $accessory_list) {
    
                    $total_item++;
                    $accessory = Accessory::find($accessory_list['accessory_id']);

                    if(empty($accessory)){
                        return $this->sendError('Accessory not found!');
                     }

                     $instock_qty = $accessory->instock_qty;  
                
                    if ( $instock_qty < 1 && $instock_qty < $accessory_list['order_qty'] ) {
                        return $this->sendError('Out of Stock');
                     }
                    
                     $accessory->instock_qty -= $accessory_list['order_qty'];
                     $accessory->save();
                    
                    if($accessory_list['selling_price'] != 0){
                        $accessory_profit = AccessoryProfit::where('accessory_id',$accessory_list['accessory_id'])
                                            ->where('color',$accessory_list['color'])
                                            ->where('date',$toady_date)
                                            ->first();

                        //Insert into Accessory Profit if customer buying cashdown and insert to profit if customer credit limit is more than voucher total
                        if ($payment_type == "cashdown") {
                            $total_acc_profits = $accessory_list['selling_price']*$accessory_list['order_qty'];
                        
                            //if cashback for disocunt is accessory
                           /* if($request->cashback_flag == 1 && $request->cashback_type == 2 && $request->supplier_id == 10){
                                if($total_acc_profits >= $cashback_even){
                                $total_acc_profits -= $cashback_even;
                                $total_cashback_reduce += $cashback_even;
                                }else{
                                    $remaining_cashback = $request->cashback - $total_cashback_reduce;
                                    $remaining_item = $request->total_quantity - $total_item;
                                    $cashback_even = round($remaining_cashback / $remaining_item,2);
                                }
                            }*/
                        
                            if (empty($accessory_profit)) {
                                AccessoryProfit::create([
                                    'voucher_id' => $voucher->id,
                                    'accessory_id' => $accessory_list['accessory_id'],
                                    'color' => $accessory_list['color'],
                                    'total_amount' => $total_acc_profits,
                                    'date' => $toady_date,
                                ]);
                            }else{
                                $accessory_profit->total_amount += $total_acc_profits;
                                $accessory_profit->save();
                            }

                        }elseif ( $payment_type == "credit" ) {
                            if ( $total_amount > $customer->credit_limit ) {
                                $credit_balance = $customer->credit_limit;
                                $for_cash = $total_amount - $customer->credit_limit;

                                if (empty($accessory_profit)) {
                                    AccessoryProfit::create([
                                        'voucher_id' => $voucher->id,
                                        'accessory_id' => $accessory_list['accessory_id'],
                                        'color' => $accessory_list['color'],
                                        'total_amount' => $for_cash,
                                        'date' => $toady_date,
                                    ]);
                                }else{
                                    $accessory_profit->total_amount += $for_cash;
                                    $accessory_profit->save();
                                }
                            }
                        }
                    }
            }
        }

        //store Customer Payment Log if voucher is with credit 
        if($payment_type == "credit"){

            $credit_balance = $customer->credit_balance;
            if($total_amount < $customer->credit_limit){
                $credit_balance = $total_amount;

                $customer_payment_log = CustomerPaymentLog::create([
                    "voucher_id" => $voucher->id,
                    "customer_id" => $customer->id,
                    "total_credit_amount" => $total_amount,
                    "total_paid_amount" => 0,
                    "remaining_amount" => $total_amount,
                    "payment_due_date" => $this->calculatePaymentDueDate($customer->allow_credit_period, time()),
                ]);
                $voucher->credit += $voucher_grand_total;
                $voucher->save();

            }else{
                $credit_balance = $customer->credit_limit;

                $customer_payment_log = CustomerPaymentLog::create([
                    "voucher_id" => $voucher->id,
                    "customer_id" => $customer->id,
                    "total_credit_amount" => $credit_balance,
                    "total_paid_amount" => 0,
                    "remaining_amount" => $credit_balance,
                    "payment_due_date" => $this->calculatePaymentDueDate($customer->allow_credit_period, time()),
                ]);

                $voucher->credit += $credit_balance;
                $voucher->save();

            }

            $customer->save();
            
        }
        
        $voucher_data = Voucher::find($voucher->id);
        return $this->sendResponse('voucher_data' , 'Successfully Sold');
    }
    
    public function saleReturn(Request $request) {
         $validator = Validator::make($request->all(), [
            'voucher_id' => 'required',
        ]);
        
        if ($validator->fails()) {
            return $this->sendError('အချက်အလက် များ မှားယွင်း နေပါသည်။');
        }
        
        $voucher = Voucher::find($request->voucher_id);
        
        if(empty($voucher)){
            return $this->sendError('Voucher not found');
        }
        
        $voucher->item_list = json_encode($request->new_itemlist)??$voucher->item_list;
        $voucher->accessory_list = json_encode($request->new_acclist)??$voucher->accessory_list;
        $voucher->total_amount = $request->total_amount;
        $voucher->voucher_grand_total = $request->voucher_grand_total;
        
        if(!empty($request->purchase_itemlist)) {
            foreach($request->purchase_itemlist as $itemlist) {
                $product = ProductWithImei::where('product_id',$itemlist['product_id'])
                        ->where('imei_number',$itemlist['imei_number'])
                        ->first();
                if(empty($product)){
                    return $this->sendError('Product not found');
                }else{
                    $product_main = Product::find($itemlist['product_id']);
                    $product_main->instock_qty -= 1;
                    $product_main->save();
                    $product->sold_flag = 1;
                    $product->save();
                }
            }
        }
        
        if(!empty($request->remove_itemlist)){
            foreach($request->remove_itemlist as $itemlist) {
                
                $product = ProductWithImei::where('product_id',$itemlist['product_id'])
                        ->where('imei_number',$itemlist['imei_number'])
                        ->first();
                $product_main = Product::find($itemlist['product_id']);
                
                $profit = ProductProfit::where('product_id',$itemlist['product_id'])
                        ->where('imei_number',$itemlist['imei_number'])
                        ->where('date',$voucher->date)
                        ->first();
                        
                if(empty($product)){
                    return $this->sendError('Product not found');
                }      
                if($profit){
                    if($profit->total_amount <= 0 ) {
                        $profit->delete();
                    }else{
                        // $profit->total_amount -= $itemlist['selling_price'] * $itemlist['order_qty'];
                        // $profit->save();
                    }
                    
                }
                
                if($itemlist['exchange_itemflag'] ==0) {
                    $product_main->instock_qty += 1;
                    $product->sold_flag = 0;
                    $product_main->save();
                    $product->save();
                }else{
                    $exchage_product = ExchangeProduct::create([
                        'product_id' => $itemlist['product_id'],
                        'imei_number' => $itemlist['imei_number'],
                        'product_name' => $itemlist['name'],
                        'voucher_number' => $voucher->voucher_number,
                        'comment' => $itemlist['comment'],
                        'exchange_date' => date('Y-m-d'),
                        'supplier_id' => $itemlist['supplier_id'],
                    ]);
                }
                
                
                
            }
        }
        //reduce stock when customer take other acccessory
        if(!empty($request->purchase_acclist)) {
            foreach($request->purchase_acclist as $acclist) {
                $accessory = Accessory::find($acclist['accessory_id']);
                if(empty($accessory)) {
                    return $this->sendError('Accessory not found');
                }else{
                    if($accessory->instock_qty < 1){
                        return $this->sendError('Out of stock');
                    }
                    $accessory->instock_qty -= $acclist['order_qty'];
                    $accessory->save();
                }
            }
        }
        //add stock or add in exchange item table when customer return the accessory
        if(!empty($request->remove_acclist)){
            foreach($request->remove_acclist as $acclist) {
                
                $acc_profit = AccessoryProfit::where('accessory_id',$acclist['accessory_id'])
                        ->where('date',$voucher->date)
                        ->first();
                $accessory = Accessory::find($acclist['accessory_id']);
                
                if($acclist['selling_price'] != 0 ){
                    // $acc_profit->total_amount -= $acclist['selling_price'] * $itemlist['order_qty'];  
                    // $acc_profit->save();
                    if($acclist['exchange_accflag'] == 0){
                        $accessory->instock_qty += $acclist['qty'];
                    }else{
                        // insert exchange item from supplier
                        $exchage_product = ExchangeProduct::create([
                            'accessory_id' => $acclist['accessory_id'],
                            'qty' => $acclist['qty'],
                            'accessory_name' => $acclist['name'],
                            'voucher_number' => $voucher->voucher_number,
                            'comment' => $acclist['comment'],
                            'exchange_date' => date('Y-m-d'),
                            'supplier_id' => $acclist['supplier_id'],
                        ]);
                    }
                    
                }
                $accessory->save();
                
            }
        }
        $voucher->save();
        
        return $this->sendResponse('voucher',$voucher);
    }
    
    public function voucherHistory(Request $request){

        $validator = Validator::make($request->all(), [
            'start_timetick' => 'required',
            'end_timetick' => 'required',
        ]);
        
        if ($validator->fails()) {
            return $this->sendError('အချက်အလက် များ မှားယွင်း နေပါသည်။');
        }
        
        if($request->has('page')){
            $page_no = $request->page;
        }else{
            $page_no = 1;
        }
        $limit = 20;
        $offset = ($page_no*$limit)-$limit;
        
        $start_timetick = $request->start_timetick;
        $end_timetick = $request->end_timetick; //add to end_timetick because of android endtimetick is the 12:00 AM of the end date
        
        $vouchers = Voucher::whereBetween('vouchers.date', array($start_timetick, $end_timetick))
        ->offset($offset)->take($limit)->orderBy('vouchers.date')->get();

        return $this->sendResponse('vouchers', $vouchers);

    }
    public function searchWithVoucherNo(Request $request){
        $validator = Validator::make($request->all(), [
            'voucher_number' => 'required',
        ]);
        
        if ($validator->fails()) {
            return $this->sendError('အချက်အလက် များ မှားယွင်း နေပါသည်။');
        }
        
        if($request->has('page')){
            $page_no = $request->page;
        }else{
            $page_no = 1;
        }
        $limit = 20;
        $offset = ($page_no*$limit)-$limit;
        
        $vouchers = Voucher::where('voucher_number',$request->voucher_number)
        ->offset($offset)->take($limit)->first();

        return $this->sendResponse('vouchers', $vouchers);
    }
    public function voucherDetail(Request $request){
        $validator = Validator::make($request->all(), [
            'voucher_id' => 'required',
        ]);
        
        if ($validator->fails()) {
            return $this->sendError('အချက်အလက် များ မှားယွင်း နေပါသည်။');
        }
        
        $voucher = Voucher::find($request->voucher_id);
        
        return $this->sendResponse('voucher',$voucher);
    }
}
