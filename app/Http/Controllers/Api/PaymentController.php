<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\apiBaseController;
use Illuminate\Http\Request;
use DateTime;
use Illuminate\Support\Facades\Validator;
use App\Payment;
use App\PaymentHistory;
use App\Aeon;

class PaymentController extends apiBaseController
{
    public function all(Request $request){
        
        $installments = Aeon::all();

        return $this->sendResponse('installments', $installments);
    }

    public function store(Request $request){
        
        $validator = Validator::make($request->all(), [
        	"product_id" => "required",
        	"selling_price" => "required",
            "applicant_name" => "required",
            "nrc" => "required",
            "job_position" => "required",
            "salary" => "required",
            "installment_plan" => "required",
            "product_id" => "required",
        ]);
        
        if ($validator->fails()) {
            return $this->sendError('အချက်အလက် များ မှားယွင်း နေပါသည်။');
        }

        $aeon = Aeon::create([
            "applicant_name" => $request->applicant_name,
            "nrc" => $request->nrc,
            "job_position" => $request->job_position,
            "salary" => $request->salary,
            "installment_plan" => $request->installment_plan,
            "product_id" => $request->product_id,
        ]);

        if ($request->hasfile('details_document')) {

			$image = $request->file('details_document');
			$name = $image->getClientOriginalName();
			$image->move(public_path() . '/image/', $name);
			$image = $name;
			$aeon->details_document = $image;
			$aeon->save();
		}

        if ($request->hasfile('job_reference_letter')) {

			$image = $request->file('job_reference_letter');
			$name = $image->getClientOriginalName();
			$image->move(public_path() . '/image/', $name);
			$image = $name;
			$aeon->job_reference_letter = $image;
			$aeon->save();
		}

        if ($request->hasfile('reporter_reference_letter')) {

			$image = $request->file('reporter_reference_letter');
			$name = $image->getClientOriginalName();
			$image->move(public_path() . '/image/', $name);
			$image = $name;
			$aeon->reporter_reference_letter = $image;
			$aeon->save();
		}

		$payment = Payment::create([
			'paid' => 0,
			'remaining_amount' => $request->selling_price,
			'customer_id' => $request->customer_id,
			'customer_name' => $request->customer_name,
			'aeon_id' => $aeon->id,
			'status' => 0,
			'customer_phone' => $request->customer_phone,

		]);

		$date = $aeon->created_at->format('y-m-d');

		$payment_id = $payment->id;

		$fee = $request->selling_price;

		if ($request->installment_plan == 3) {

			$eachmonth = $fee / 9;

			$month = round($eachmonth);

			$tenThousands = round($month % 100000 / 10);

			$month1 = $month - $tenThousands;

			$final = $month1 * 8;

			$final_pay = $fee - $final;

			$name = array("1st Payment", "2nd Payment", "3rd Payment", "4th Payment", "5th Payment", "6th Payment", "7th Payment", "8th Payment", "9th Payment");

			for ($i = 0; $i < 9; $i++) {

				if ($i == 8) {
					$payment_history = PaymentHistory::create([
						'name' => $name[$i],
						'payment_amount' => $final_pay,
						'status' => "Not Paid",
						'due_date' => $date,
						'payment_id' => $payment_id,
					]);

				} else {
					$payment_history = PaymentHistory::create([
						'name' => $name[$i],
						'payment_amount' => $month1,
						'status' => "Not Paid",
						'due_date' => $date,
						'payment_id' => $payment_id,
					]);
				}

				$time2 = strtotime($date);

				$date = date("y-m-d", strtotime("+1 month", $time2));

			}
		} elseif ($request->installment_plan == 1) {
			
			$eachmonth = $fee / 3;

			$month = round($eachmonth);

			$tenThousands = round($month % 100000 / 10);

			$month1 = $month - $tenThousands;

			$final = $month1 * 2;

			$final_pay = $fee - $final;

			$name = array("1st Payment", "2nd Payment", "3rd Payment");

			for ($i = 0; $i < 3; $i++) {

				if ($i == 2) {
					$payment_history = PaymentHistory::create([
						'name' => $name[$i],
						'payment_amount' => $final_pay,
						'status' => "Not Paid",
						'due_date' => $date,
						'payment_id' => $payment_id,
					]);

				} else {
					$payment_history = PaymentHistory::create([
						'name' => $name[$i],
						'payment_amount' => $month1,
						'status' => "Not Paid",
						'due_date' => $date,
						'payment_id' => $payment_id,
					]);
				}

				$time2 = strtotime($date);

				$date = date("y-m-d", strtotime("+1 months", $time2));

			}
		} elseif ($request->installment_plan == 2) {

			$eachmonth = $fee / 6;

			$month = round($eachmonth);

			$tenThousands = round($month % 100000 / 10);

			$month1 = $month - $tenThousands;

			$final = $month1 * 5;

			$final_pay = $fee - $final;

			$name = array("1st Payment", "2nd Payment", "3rd Payment", "4th Payment", "5th Payment", "6th Payment");

			for ($i = 0; $i < 6; $i++) {

				if ($i == 5) {
					$payment_history = PaymentHistory::create([
						'name' => $name[$i],
						'payment_amount' => $final_pay,
						'status' => "Not Paid",
						'due_date' => $date,
						'payment_id' => $payment_id,
					]);

				} else {
					$payment_history = PaymentHistory::create([
						'name' => $name[$i],
						'payment_amount' => $month1,
						'status' => "Not Paid",
						'due_date' => $date,
						'payment_id' => $payment_id,
					]);
				}

				$time2 = strtotime($date);

				$date = date("y-m-d", strtotime("+1 months", $time2));

			}

		}elseif ($request->installment_plan == 4) {

			$eachmonth = $fee / 12;

			$month = round($eachmonth);

			$tenThousands = round($month % 100000 / 10);

			$month1 = $month - $tenThousands;

			$final = $month1 * 11;

			$final_pay = $fee - $final;

			$name = array("1st Payment", "2nd Payment", "3rd Payment", "4th Payment", "5th Payment", "6th Payment","7th Payment","8th Payment","9th Payment","10th Payment","11th Payment","12th Payment");

			for ($i = 0; $i < 12; $i++) {

				if ($i == 11) {
					$payment_history = PaymentHistory::create([
						'name' => $name[$i],
						'payment_amount' => $final_pay,
						'status' => "Not Paid",
						'due_date' => $date,
						'payment_id' => $payment_id,
					]);

				} else {
					$payment_history = PaymentHistory::create([
						'name' => $name[$i],
						'payment_amount' => $month1,
						'status' => "Not Paid",
						'due_date' => $date,
						'payment_id' => $payment_id,
					]);
				}

				$time2 = strtotime($date);

				$date = date("y-m-d", strtotime("+1 months", $time2));

			}

		} else {

			$payment_history = PaymentHistory::create([
				'name' => "Cash Down!",
				'payment_amount' => $fee,
				'status' => "Not Paid",
				'due_date' => $date,
				'payment_id' => $payment_id,
			]);
		}

		$payment_due = PaymentHistory::where('payment_id',$payment->id)->get();
        
        return $this->sendResponse('payment_due', $payment_due);
    }

    public function getAeonPaymentPlan(Request $request){
        
        $validator = Validator::make($request->all(), [
        	"selling_price" => "required",
            "installment_plan" => "required",
        ]);
        
        if ($validator->fails()) {
            return $this->sendError('အချက်အလက် များ မှားယွင်း နေပါသည်။');
        }

        $date = date('Y-m-d');

		$fee = $request->selling_price;

		if ($request->installment_plan == 3) {

			$eachmonth = $fee / 9;

			$month = round($eachmonth);

			$tenThousands = round($month % 100000 / 10);

			$month1 = $month - $tenThousands;

			$final = $month1 * 8;

			$final_pay = $fee - $final;

			$name = array("1st Payment", "2nd Payment", "3rd Payment", "4th Payment", "5th Payment", "6th Payment", "7th Payment", "8th Payment", "9th Payment");

			$payment_plan = array();

			for ($i = 0; $i < 9; $i++) {

				$time2 = strtotime($date);

				$date = date("d-m-Y", strtotime("+1 month", $time2));

				if ($i == 8) {

					$payment_plan[$name[$i]] = $name[$i]." is ".$final_pay." at ".$date;


				} else {

					$payment_plan[$name[$i]] = $name[$i]." is ".$month1." at ".$date;

				}

			}
		} elseif ($request->installment_plan == 1) {
			
			$eachmonth = $fee / 3;

			$month = round($eachmonth);

			$tenThousands = round($month % 100000 / 10);

			$month1 = $month - $tenThousands;

			$final = $month1 * 2;

			$final_pay = $fee - $final;

			$name = array("1st Payment", "2nd Payment", "3rd Payment");

			$payment_plan = array();

			for ($i = 0; $i < 3; $i++) {

				$time2 = strtotime($date);

				$date = date("d-m-Y", strtotime("+1 month", $time2));

				if ($i == 2) {

					$payment_plan[$name[$i]] = $name[$i]." is ".$final_pay." at ".$date;

				} else {

					$payment_plan[$name[$i]] = $name[$i]." is ".$month1." at ".$date;

				}

			}
		} elseif ($request->installment_plan == 2) {

			$eachmonth = $fee / 6;

			$month = round($eachmonth);

			$tenThousands = round($month % 100000 / 10);

			$month1 = $month - $tenThousands;

			$final = $month1 * 5;

			$final_pay = $fee - $final;

			$name = array("1st Payment", "2nd Payment", "3rd Payment", "4th Payment", "5th Payment", "6th Payment");

			$payment_plan = array();

			for ($i = 0; $i < 6; $i++) {

				$time2 = strtotime($date);

				$date = date("d-m-Y", strtotime("+1 month", $time2));

				if ($i == 5) {

					$payment_plan[$name[$i]] = $name[$i]." is ".$final_pay." at ".$date;

				} else {

					$payment_plan[$name[$i]] = $name[$i]." is ".$month1." at ".$date;

				}

			}

		}elseif ($request->installment_plan == 4) {

			$eachmonth = $fee / 12;

			$month = round($eachmonth);

			$tenThousands = round($month % 100000 / 10);

			$month1 = $month - $tenThousands;

			$final = $month1 * 11;

			$final_pay = $fee - $final;

			$name = array("1st Payment", "2nd Payment", "3rd Payment", "4th Payment", "5th Payment", "6th Payment","7th Payment","8th Payment","9th Payment","10th Payment","11th Payment","12th Payment");

			$payment_plan = array();

			for ($i = 0; $i < 12; $i++) {

				$time2 = strtotime($date);

				$date = date("d-m-Y", strtotime("+1 month", $time2));

				if ($i == 11) {
				$payment_plan[$name[$i]] = $name[$i]." is ".$final_pay." at ".$date;

				} else {

					$payment_plan[$name[$i]] = $name[$i]." is ".$month1." at ".$date;

				}

			}

		}
        
        return $this->sendResponse('payment_due', $payment_plan);
    }
    
    public function getAeonPaymentList(Request $request){

    	$validator = Validator::make($request->all(), [
        	"aeon_id" => "required",
        ]);
        
        if ($validator->fails()) {
            return $this->sendError('အချက်အလက် များ မှားယွင်း နေပါသည်။');
        }

    	$payment = Payment::where('aeon_id',$request->aeon_id)->first();

    	return $this->sendResponse('aeon_payment',$payment);

    }

    public function getAeonPaymentHistory(Request $request){

    	$validator = Validator::make($request->all(), [
        	"payment_id" => "required",
        ]);
        
        if ($validator->fails()) {
            return $this->sendError('အချက်အလက် များ မှားယွင်း နေပါသည်။');
        }

    	$payment_histories = PaymentHistory::where('payment_id',$request->payment_id)->get();

    	return $this->sendResponse('payment_history',$payment_histories);

    }
}
