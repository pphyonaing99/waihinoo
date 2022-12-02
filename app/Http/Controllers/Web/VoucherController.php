<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Voucher;
use Datatables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\ProductProfit;
use App\ProductWithImei;
use App\AccessoryProfit;
use App\Accessory;

class VoucherController extends Controller
{
    public function voucherList(){

    	$voucher_list = Voucher::orderBy('id','desc')->get();
        $month = date('m');
        $today_date = date("Y-m-d");
        $last_date = "2020-12-13";
        
        $total_sales = DB::table('vouchers')->select(DB::raw('COALESCE(SUM(voucher_grand_total)) as total'))->get();
        $product_profits = DB::table('product_profits')->select(DB::raw('COALESCE(SUM(total_amount)) as total'))->get();
        $accessory_profits = DB::table('accessory_profits')->select(DB::raw('COALESCE(SUM(total_amount)) as total'))->get();
        $monthly_sales = DB::table('vouchers')->select(DB::raw('COALESCE(SUM(voucher_grand_total)) as total'))->whereMonth('date',$month)->get();
        //$product_cashback = DB::table('supplier_cashbacks')->select(DB::raw('COALESCE(SUM(cashback)) as total'))->where('item_flag',1)->where('supplier_id',10)->get();
        $acc_cashback = DB::table('supplier_cashbacks')->select(DB::raw('COALESCE(SUM(cashback)) as total'))->where('item_flag',2)->where('supplier_id',10)->get();
        
        $pp = ProductProfit::all();
        $ap = AccessoryProfit::all();
        $ph_sales_revenues = 0;
        $ph_purchase_price = 0;
        $acc_purchase_price = 0;
        foreach($pp as $p){
            $product_with_imei = ProductWithImei::where('imei_number',$p->imei_number)->first();
            
            if(!empty($product_with_imei)){
                $ph_qty = $p->total_amount / $product_with_imei->sales_price;
                $ph_purchase_price += ($product_with_imei->purchase_price * $ph_qty);
            }
        }
        foreach($ap as $a){
            $accessory = Accessory::find($a->accessory_id);
                if($accessory->sales_price != 0){
                $qty = $a->total_amount / $accessory->sales_price;
                $acc_purchase_price += ($accessory->purchase_price * $qty);
                }
        }
    
        $total_revenues = round($total_sales[0]->total - ($ph_purchase_price + $acc_purchase_price));
        $accessory_profits[0]->total -= $acc_cashback[0]->total;
        $ph_sales_revenues = round($product_profits[0]->total - $ph_purchase_price);
        $acc_sales_revenues = round($accessory_profits[0]->total - $acc_purchase_price - $acc_cashback[0]->total);
        
    	return view('voucher_list',compact('total_sales','product_profits','accessory_profits','voucher_list','monthly_sales','total_revenues','ph_sales_revenues','acc_sales_revenues'));

    }

    public function getVoucherList(Request $request){

    	$vouchers = Voucher::select('voucher_number', 'sold_by', 'payment_type', 'voucher_grand_total', 'id')
			->get();

		return datatables()->of($vouchers)
            ->make(true);

    }
    
    public function voucherHistory(Request $request){

        $validator = Validator::make($request->all(), [
            'start_timetick' => 'required',
            'end_timetick' => 'required',
        ]);
        
        if($request->has('page')){
            $page_no = $request->page;
        }else{
            $page_no = 1;
        }
        
        $month = date('m');
        
        $today_date = date("Y-m-d");
        $last_date = "2020-12-13";
        

        $total_sales = DB::table('vouchers')->select(DB::raw('COALESCE(SUM(voucher_grand_total)) as total'))->get();
        $product_profits = DB::table('product_profits')->select(DB::raw('COALESCE(SUM(total_amount)) as total'))->get();
        $accessory_profits = DB::table('accessory_profits')->select(DB::raw('COALESCE(SUM(total_amount)) as total'))->get();
        $monthly_sales = DB::table('vouchers')->select(DB::raw('COALESCE(SUM(voucher_grand_total)) as total'))->whereMonth('date',$month)->get();
        //$product_cashback = DB::table('supplier_cashbacks')->select(DB::raw('COALESCE(SUM(cashback)) as total'))->where('item_flag',1)->where('supplier_id',10)->get();
        $acc_cashback = DB::table('supplier_cashbacks')->select(DB::raw('COALESCE(SUM(cashback)) as total'))->where('item_flag',2)->where('supplier_id',10)->get();
        
        
        $pp = ProductProfit::all();
        $ap = AccessoryProfit::all();
        $ph_sales_revenues = 0;
        $ph_purchase_price = 0;
        $acc_purchase_price = 0;
        foreach($pp as $p){
            $product_with_imei = ProductWithImei::where('imei_number',$p->imei_number)->first();
            
            if(!empty($product_with_imei)){
                $ph_qty = $p->total_amount / $product_with_imei->sales_price;
                $ph_purchase_price += ($product_with_imei->purchase_price * $ph_qty);
            }
        }
        foreach($ap as $a){
            $accessory = Accessory::find($a->accessory_id);
                if($accessory->sales_price){
                $qty = $a->total_amount / $accessory->sales_price;
                $acc_purchase_price += ($accessory->purchase_price * $qty);
                }
        }
    
        $total_revenues = round($total_sales[0]->total - ($ph_purchase_price + $acc_purchase_price));
        $accessory_profits[0]->total -= $acc_cashback[0]->total;
        $ph_sales_revenues = round($product_profits[0]->total - $ph_purchase_price);
        $acc_sales_revenues = round($accessory_profits[0]->total - $acc_purchase_price - $acc_cashback[0]->total);

        
        $limit = 20;
        $offset = ($page_no*$limit)-$limit;
        
        $start_timetick = $request->start_timetick;
        $end_timetick = $request->end_timetick; //add to end_timetick because of android endtimetick is the 12:00 AM of the end date
        
        $vouchers = Voucher::whereBetween('vouchers.date', array($start_timetick, $end_timetick))
        ->offset($offset)->take($limit)->orderBy('vouchers.date')->get();

        return view('voucher_history',compact('vouchers','total_sales','product_profits','accessory_profits','monthly_sales','total_revenues','ph_sales_revenues','acc_sales_revenues'));

    }
    public function voucher(Request $request , $voucher_id ){

        $voucher = Voucher::find($voucher_id);
        
    	return view('invoice',compact('voucher'));

    }
    public function getDailyProfit(Request $request){
        
        $today_date = date("Y-m-d");
        $last_date = "2020-12-13";
        
        
        $total_sales = DB::table('vouchers')->select(DB::raw('COALESCE(SUM(voucher_grand_total)) as total'))->where('date',$request->date)->get();
        $product_profits = DB::table('product_profits')->select(DB::raw('COALESCE(SUM(total_amount)) as total'))->where('date',$request->date)->get();
        $accessory_profits = DB::table('accessory_profits')->select(DB::raw('COALESCE(SUM(total_amount)) as total'))->where('date',$request->date)->get();
        //$product_cashback = DB::table('supplier_cashbacks')->select(DB::raw('COALESCE(SUM(cashback)) as total'))->where('item_flag',1)->where('supplier_id',10)->where('date',$request->date)->get();
        $acc_cashback = DB::table('supplier_cashbacks')->select(DB::raw('COALESCE(SUM(cashback)) as total'))->where('item_flag',2)->where('supplier_id',10)->where('date',$request->date)->get();
        
        $pp = ProductProfit::where('date',$request->date)->get();
        $ap = AccessoryProfit::where('date',$request->date)->get();
        $ph_sales_revenues = 0;
        $ph_purchase_price = 0;
        $acc_purchase_price = 0;
        foreach($pp as $p){
            $product_with_imei = ProductWithImei::where('imei_number',$p->imei_number)->first();
            
            if(!empty($product_with_imei)){
                $ph_qty = $p->total_amount / $product_with_imei->sales_price;
                $ph_purchase_price += ($product_with_imei->purchase_price * $ph_qty);
            }
        }
        foreach($ap as $a){
            $accessory = Accessory::find($a->accessory_id);
            
                $qty = $a->total_amount / $accessory->sales_price;
                $acc_purchase_price += ($accessory->purchase_price * $qty);
        }
        // return response()->json($total_sales);
        $total_sales = intval($product_profits[0]->total) + intval($accessory_profits[0]->total);
        $total_revenues = $total_sales - ($ph_purchase_price + $acc_purchase_price);
        
       $accessory_profits[0]->total -= $acc_cashback[0]->total;
       $ph_sales_revenues = round($product_profits[0]->total - $ph_purchase_price);
       $acc_sales_revenues = round($accessory_profits[0]->total - $acc_purchase_price - $acc_cashback[0]->total);

        
        $voucher = Voucher::where('date',$request->date)->get();
        
        return response()->json([
            'voucher' => $voucher,
            'total_sales' => $total_sales,
            'product_profits' => intval($product_profits[0]->total),
            'accessory_profits' => intval($accessory_profits[0]->total),
            'total_revenues' => $total_revenues,
            'ph_sales_revenues' => $ph_sales_revenues,
            'acc_sales_revenues' => $acc_sales_revenues,
        ]);
    }
    public function getMonthlyProfit(Request $request){
        
        $today_date = date("Y-m-d");
        $last_date = "2020-12-13";
        
        $total_sales = DB::table('vouchers')->select(DB::raw('COALESCE(SUM(voucher_grand_total)) as total'))->whereMonth('date',$request->month)->get();
        $product_profits = DB::table('product_profits')->select(DB::raw('COALESCE(SUM(total_amount)) as total'))->whereMonth('date',$request->month)->get();
        $accessory_profits = DB::table('accessory_profits')->select(DB::raw('COALESCE(SUM(total_amount)) as total'))->whereMonth('date',$request->month)->get();
        //$product_cashback = DB::table('supplier_cashbacks')->select(DB::raw('COALESCE(SUM(cashback)) as total'))->where('item_flag',1)->where('supplier_id',10)->whereMonth('date',$request->month)->get();
        $acc_cashback = DB::table('supplier_cashbacks')->select(DB::raw('COALESCE(SUM(cashback)) as total'))->where('item_flag',2)->where('supplier_id',10)->whereMonth('date',$request->month)->get();
        
        $pp = ProductProfit::whereMonth('date',$request->month)->get();
        $ap = AccessoryProfit::whereMonth('date',$request->month)->get();
        $ph_sales_revenues = 0;
        $ph_purchase_price = 0;
        $acc_purchase_price = 0;
        foreach($pp as $p){
            $product_with_imei = ProductWithImei::where('imei_number',$p->imei_number)->first();
            
            if(!empty($product_with_imei)){
                $ph_qty = $p->total_amount / $product_with_imei->sales_price;
                $ph_purchase_price += ($product_with_imei->purchase_price * $ph_qty);
            }
        }
        foreach($ap as $a){
            $accessory = Accessory::find($a->accessory_id);
            
                $qty = $a->total_amount / $accessory->sales_price;
                $acc_purchase_price += ($accessory->purchase_price * $qty);
        }
        $total_sales = intval($product_profits[0]->total) + intval($accessory_profits[0]->total);
        $total_revenues = round($total_sales - ($ph_purchase_price + $acc_purchase_price));
        $accessory_profits[0]->total -= $acc_cashback[0]->total;
        $ph_sales_revenues = round($product_profits[0]->total - $ph_purchase_price);
        $acc_sales_revenues = round($accessory_profits[0]->total - $acc_purchase_price - $acc_cashback[0]->total);

        
        $voucher = Voucher::whereMonth('date',$request->month)->get();
        
        return response()->json([
            'voucher' => $voucher,
            'total_sales' => $total_sales,
            'product_profits' => intval($product_profits[0]->total),
            'accessory_profits' => intval($accessory_profits[0]->total),
            'total_revenues' => $total_revenues,
            'ph_sales_revenues' => $ph_sales_revenues,
            'acc_sales_revenues' => $acc_sales_revenues,
        ]);
    }
}
