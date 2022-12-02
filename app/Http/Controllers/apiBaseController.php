<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class apiBaseController extends Controller
{
    
    public function calculatePaymentDueDate($week_count, $start_timetick){
        $start_timetick = $start_timetick??time();
        $due_timetick = $start_timetick+($week_count * 7 * 86400);
        $due_date = date('Y/m/d', $due_timetick);
        return $due_date;
    }

    public function get_user_token( $user, string $token_name = null ) {

         return $user->createToken($token_name)->accessToken; 

      }
    
    protected function sendResponse($name, $data = [], $message = "successful", $additional_data = [])
    {
        $response = [
            $name => $data,
            'success' => true,
            'message' => $message
        ];
        
        if(count($additional_data) > 0){
            $response = array_merge($response, $additional_data);   
        }
        return response()->json($response);
    }

    public function sendError($errorMessage = [])
    {
        $response = [
            'success' => false,
            'message' => $errorMessage
        ];


        return response()->json($response);
    }
    
    protected function generateCode($prefix, $id){
        return $prefix.sprintf("%04s", $id);
    }
}
