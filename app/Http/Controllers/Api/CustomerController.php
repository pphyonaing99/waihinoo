<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\apiBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Customer;
use App\CustomerPaymentLog;
use App\CustomerRepaymentLog;
use App\User;
use Illuminate\Support\Str;

class CustomerController extends apiBaseController
{
    public function all(Request $request){
        
        $customers = Customer::orderBy('id','desc')->get();

        return $this->sendResponse('customers', $customers);
    }

    public function store(Request $request){
        
        $validator = Validator::make($request->all(), [
            "name" => "required",
            "address" => "required",
            "phone" => "required",
        ]);

        if ($validator->fails()) {
            return $this->sendError('အချက်အလက် များ မှားယွင်း နေပါသည်။');
        }
        
        $customer = Customer::create([
            "name" => $request->name,
            "address" => $request->address,
            "phone" => $request->phone,
            'advance_balance' => $request->advance_balance,
            'credit_balance' => $request->credit_balance,
            'created_by' => $request->created_by,
            'frequent_item' => $request->frequent_item == null? "": $request->frequent_item,
            'credit_flag' => $request->credit_flag??0,
            'credit_limit' => $request->credit_limit??0,
            'allow_credit_period' => $request->allow_credit_period,
        ]);

        if ($request->has('email')) {
            $customer->email = $request->email;
            $customer->save();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => \Hash::make($request->password),
            'remember_token' => Str::random(60),
        ]);

        $customer->user_id = $user->id;
        $customer->save();
        
        $customer['token'] =  $this->get_user_token($user,"Personal Access Token");

        if ($request->has('role')) {
            $user->assignRole($request->role);
            $user->save();
        }
        
        return $this->sendResponse('customer', $customer);
    }

    public function update(Request $request){

        $validator = Validator::make($request->all(), [
            "customer_id" => "required",
            "name" => "required",
            "phone" => "required",
        ]);
        
        if ($validator->fails()) {
            return $this->sendError('အချက်အလက် များ မှားယွင်း နေပါသည်။');
        }

        $customer = Customer::find($request->customer_id);

        if (empty($customer)) {
            return $this->sendError('Customer not found');
        }

        $customer->name = $request->name;
        $customer->phone = $request->phone;
        $customer->address = $request->address;
        $customer->advance_balance = $request->advance_balance;
        $customer->save();

        return $this->sendResponse('customer',$customer);
    }

    public function checkCustomerCredit(Request $request){
        
        $customer_credit = CustomerPaymentLog::all();
        
        return $this->sendResponse('customer_credit',$customer_credit);
        
    }
    
    public function checkCustomerCreditDetails(Request $request){
        
        $customer_credit_details = CustomerPaymentLog::where('customer_id',$request->customer_id)->get();
        
        return $this->sendResponse('customer_credit_details',$customer_credit_details);
        
    }

    public function storeCustomerPaymentLog(Request $request){
        $validator = Validator::make($request->all(), [
            "voucher_id" => "required", 
            "customer_id" => "required",
            "total_credit_amount" => "required"
            ]);
        
        if ($validator->fails()) {
            return $this->sendError('အချက်အလက် များ မှားယွင်း နေပါသည်။');
        }

        $customer = Customer::find($request->customer_id);
        $customer_payment_log = CustomerPaymentLog::create([
            "voucher_id" => $request->voucher_id,
            "customer_id" => $request->customer_id,
            "total_credit_amount" => $request->total_credit_amount,
            "total_paid_amount" => 0,
            "remaining_amount" => $request->total_credit_amount,
            "payment_due_date" => $this->calculatePaymentDueDate($customer->allow_credit_period, time()),
        ]);
        
        return $this->sendResponse("customer_payment_log", $customer_payment_log);
        
    }
    
    public function getCustomerPaymentLog(Request $request){
        
        $customer_payment_logs = CustomerPaymentLog::all();
        
        return $this->sendResponse("customer_payment_logs", $customer_payment_logs);
    }
    
    public function storeCustomerRepaymentLog(Request $request){
        $validator = Validator::make($request->all(), [
            "customer_payment_log_id" => "required",
            "paid_amount" => "required",
            "customer_id" => "required",
            "paid_timetick" => "required",
        ]);

        if ($validator->fails()) {
            return $this->sendError('အချက်အလက် များ မှားယွင်း နေပါသည်။');
        }

        $payment = CustomerPaymentLog::find($request->customer_payment_log_id);

        if(empty($payment)){
            return $this->sendError("Payment not found");
        }
        
        if($payment->remaining_amount < $request->paid_amount){
            return $this->sendError("Paid amount cannot be more than the remaining credit amount");
        }

        $repayment = CustomerRepaymentLog::create([
            "customer_id" => $payment->customer_id,
            "customer_payment_log_id" => $request->customer_payment_log_id,
            "paid_amount" => $request->paid_amount,
            "paid_timetick" => $request->paid_timetick,
        ]);

        $payment->total_paid_amount = $payment->total_paid_amount + (int)$request->paid_amount;
        $payment->remaining_amount = $payment->remaining_amount - (int)$request->paid_amount;
        $payment->save();
        
        $customer = Customer::find($request->customer_id);
        $customer->credit_balance = $customer->credit_balance - $request->paid_amount;
        $customer->save();
        
        return $this->sendResponse("repayment_history", $repayment);
    }
    
    public function getCustomerRepaymentLog(Request $request){
        
        $customer_repayment_logs = CustomerRepaymentLog::all();
        
        return $this->sendResponse("customer_repayment_logs", $customer_repayment_logs);
    }
    public function paymentToken(Request $request){

        //Request information 
        $api_env = config('2c2p.sandbox').'/paymentToken';

        //Set API request version
        $api_version = "10.01"; 
        //Generate an unique random string
        $nonce_str = uniqid('', true);  

        //Merchant's account information
        //Get MerchantID when opening account with 2C2P
        $secret_key = config('2c2p.secret_key');
        $mid = config('2c2p.merchant_id');
        
        //Transaction information
        //Set product description
        // $desc = "2 days 1 night hotel room"; 
        //Set an unique invoice no
        $invoice_no = time(); 
        //Set currency code in 3 alphabet values as specified in ISO 4217
        // $currency_code = "SGD"; 
        //Amount formatted into 12-digit format with leading zero as specified in ISO 4217
        // $amount = "000000001000";

        //Set payment options
        //Set payment channel
        $payment_channel = "ALL";
        //Set credit card 3D Secure mode
        $request_3ds = config('2c2p.cardSecureMode_no');
        //Enable Card tokenization without authorization
        // $tokenize_only = "Y";

        //Set advance payment options
        //IPP
        // $interest_type = "M";

        //Recurring
        $recurring = "Y";               //Enable / Disable RPP option
        $invoice_prefix = 'demo'.time();            //RPP transaction invoice prefix
        $recurring_amount = "000000000100";     //Recurring amount
        $allow_accumulate = "Y";                //Allow failed authorization to be accumulated
        $max_accumulateAmt = "000000001000";                //Maximum threshold of total accumulated amount
        $recurring_interval = "5";          //Recurring interval by no of days
        $recurring_count = "3";             //Number of Recurring occurance
        $charge_next_date = (new DateTime('tomorrow'))->format("dmY");  //The first day to start recurring charges. format DDMMYYYY

        //---------------------------------- Request ---------------------------------------//

        //Construct payment token request
        $payment_token_request = new \stdClass();
        $payment_token_request->version = $api_version;
        $payment_token_request->merchantID = $mid;
        $payment_token_request->invoiceNo = $invoice_no;
        $payment_token_request->desc = $request->description;
        $payment_token_request->amount = sprintf("%012s", $request->amount);
        $payment_token_request->currencyCode = $request->currency_code;
        $payment_token_request->paymentChannel = $payment_channel;
        // $payment_token_request->userDefined1 = "This is my user defined 1";
        // $payment_token_request->userDefined2 = "This is my user defined 2";
        // $payment_token_request->userDefined3 = "This is my user defined 3";
        // $payment_token_request->userDefined4 = "This is my user defined 4";
        // $payment_token_request->userDefined5 = "This is my user defined 5";
        // $payment_token_request->interestType = $interest_type;
        // $payment_token_request->productCode = "";
        // $payment_token_request->recurring = $recurring;
        // $payment_token_request->invoicePrefix = $invoice_prefix;
        // $payment_token_request->recurringAmount = $recurring_amount;
        // $payment_token_request->allowAccumulate = $allow_accumulate;
        // $payment_token_request->maxAccumulateAmt = $max_accumulateAmt;
        // $payment_token_request->recurringInterval = $recurring_interval;
        // $payment_token_request->recurringCount = $recurring_count;
        // $payment_token_request->chargeNextDate = $charge_next_date;
        // $payment_token_request->promotion = "";
        $payment_token_request->request3DS = $request_3ds;
        // $payment_token_request->tokenizeOnly = $tokenize_only;
        // $payment_token_request->statementDescriptor = "";
        $payment_token_request->nonceStr = $nonce_str;

        //Important: Generate signature
        //Init 2C2P PaymentGatewayHelper

        //Generate signature of payload
        $hashed_signature = generateSignature($payment_token_request, $secret_key); 

        //Set hashed signature
        $payment_token_request->signature = $hashed_signature;

        //---------------------------------- Response ---------------------------------------//

        //Do Payment Token API request
        $encoded_payment_token_response = requestAPI($api_env, $payment_token_request);
        // echo $encoded_payment_token_response;
        // print_r($encoded_payment_token_response);
        //Important: Verify response signature
        $is_valid_signature = validateSignature($encoded_payment_token_response, $secret_key);

        if($is_valid_signature) {

            //Parse api response
            $payment_token_response = parseAPIResponse($encoded_payment_token_response);
            
            //Get payment token and pass token to your mobile application.
            $payment_token = $payment_token_response;
            return response()->json($payment_token);
        } else {

            //Return encoded error response
            return response()->json("error");
        }

    }

    public function paymentTokenWith3ds(Request $request){

        //Request information 
        $api_env = config('2c2p.sandbox').'/paymentToken';

        //Set API request version
        $api_version = "10.01"; 
        //Generate an unique random string
        $nonce_str = uniqid('', true);  

        //Merchant's account information
        //Get MerchantID when opening account with 2C2P
        $secret_key = config('2c2p.secret_key');
        $mid = config('2c2p.merchant_id');
        
        //Transaction information
        //Set product description
        // $desc = "2 days 1 night hotel room"; 
        //Set an unique invoice no
        $invoice_no = time(); 
        //Set currency code in 3 alphabet values as specified in ISO 4217
        // $currency_code = "SGD"; 
        //Amount formatted into 12-digit format with leading zero as specified in ISO 4217
        // $amount = "000000001000";

        //Set payment options
        //Set payment channel
        $payment_channel = "ALL";
        //Set credit card 3D Secure mode
        $request_3ds = config('2c2p.cardSecureMode_no');
        //Enable Card tokenization without authorization
        // $tokenize_only = "Y";

        //Set advance payment options
        //IPP
        // $interest_type = "M";

        //Recurring
        $recurring = "Y";               //Enable / Disable RPP option
        $invoice_prefix = 'demo'.time();            //RPP transaction invoice prefix
        $recurring_amount = "000000000100";     //Recurring amount
        $allow_accumulate = "Y";                //Allow failed authorization to be accumulated
        $max_accumulateAmt = "000000001000";                //Maximum threshold of total accumulated amount
        $recurring_interval = "5";          //Recurring interval by no of days
        $recurring_count = "3";             //Number of Recurring occurance
        $charge_next_date = (new DateTime('tomorrow'))->format("dmY");  //The first day to start recurring charges. format DDMMYYYY

        //---------------------------------- Request ---------------------------------------//

        //Construct payment token request
        $payment_token_request = new \stdClass();
        $payment_token_request->version = $api_version;
        $payment_token_request->merchantID = $mid;
        $payment_token_request->invoiceNo = $invoice_no;
        $payment_token_request->desc = $request->description;
        $payment_token_request->amount = sprintf("%012s", $request->amount);
        $payment_token_request->currencyCode = $request->currency_code;
        $payment_token_request->paymentChannel = $payment_channel;
        // $payment_token_request->userDefined1 = "This is my user defined 1";
        // $payment_token_request->userDefined2 = "This is my user defined 2";
        // $payment_token_request->userDefined3 = "This is my user defined 3";
        // $payment_token_request->userDefined4 = "This is my user defined 4";
        // $payment_token_request->userDefined5 = "This is my user defined 5";
        // $payment_token_request->interestType = $interest_type;
        // $payment_token_request->productCode = "";
        // $payment_token_request->recurring = $recurring;
        // $payment_token_request->invoicePrefix = $invoice_prefix;
        // $payment_token_request->recurringAmount = $recurring_amount;
        // $payment_token_request->allowAccumulate = $allow_accumulate;
        // $payment_token_request->maxAccumulateAmt = $max_accumulateAmt;
        // $payment_token_request->recurringInterval = $recurring_interval;
        // $payment_token_request->recurringCount = $recurring_count;
        // $payment_token_request->chargeNextDate = $charge_next_date;
        // $payment_token_request->promotion = "";
        $payment_token_request->request3DS = $request_3ds;
        // $payment_token_request->tokenizeOnly = $tokenize_only;
        // $payment_token_request->statementDescriptor = "";
        $payment_token_request->nonceStr = $nonce_str;

        //Important: Generate signature
        //Init 2C2P PaymentGatewayHelper

        //Generate signature of payload
        $hashed_signature = generateSignature($payment_token_request, $secret_key); 

        //Set hashed signature
        $payment_token_request->signature = $hashed_signature;

        //---------------------------------- Response ---------------------------------------//

        //Do Payment Token API request
        $encoded_payment_token_response = requestAPI($api_env, $payment_token_request);
        // echo $encoded_payment_token_response;
        // print_r($encoded_payment_token_response);
        //Important: Verify response signature
        $is_valid_signature = validateSignature($encoded_payment_token_response, $secret_key);

        if($is_valid_signature) {

            //Parse api response
            $payment_token_response = parseAPIResponse($encoded_payment_token_response);
            
            //Get payment token and pass token to your mobile application.
            $payment_token = $payment_token_response;
            return response()->json($payment_token);
        } else {

            //Return encoded error response
            return response()->json("error");
        }

    }
    public function paymentInquiry(Request $request){

        $api_env = config('2c2p.sandbox').'/paymentInquiry';

        //Request information 
        $api_version = "1.1";

        //Merchant's account information
        $secret_key = config('2c2p.secret_key');
        $mid = config('2c2p.merchant_id');

        //Inquiry information
        $transaction_id = "1345111";

        //Construct payment inquiry request
        $payment_inquiry_request = new \stdClass();
        $payment_inquiry_request->version = $api_version;
        $payment_inquiry_request->merchantID = $mid;
        $payment_inquiry_request->transactionID = $transaction_id;

        //Important: Generate signature

        $hashed_signature = generateSignature($payment_inquiry_request, $secret_key);
        $payment_inquiry_request->signature = $hashed_signature;

        //Do Payment Inquiry API request
        $encoded_payment_inquiry_response = requestAPI($api_env, $payment_inquiry_request); 

        //Important: Verify response signature
        $is_valid_signature = validateSignature($encoded_payment_inquiry_response, $secret_key);
        if($is_valid_signature) {

            //Valid signature, get payment result
            $payment_inquiry_response = parseAPIResponse($encoded_payment_inquiry_response);
            /*$invoice_no = $payment_inquiry_response->invoiceNo;
            $resp_code = $payment_inquiry_response->respCode;*/

            return response()->json($payment_inquiry_response);

        } else {
            //Invalid signature, return error response
            return response()->json("HAHA");
        }
    }
}
