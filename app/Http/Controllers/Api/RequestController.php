<?php

namespace App\Http\Controllers\Api;

use App\Events\RequestConfirmAmountNotify;
use App\Events\RequestNotify;
use App\Events\RequestRefusedAmountNotify;
use App\Http\Controllers\Controller;
use App\Models\Amountreq;
use App\Models\Contact;
use App\Models\User;
use App\Services\FCMService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use Carbon\Carbon;




class RequestController extends Controller
{

    public function get_amount_request()

    { 
        
        try {
            $all_amount_request = Amountreq::where(function ($query)  {
                $query->where('to_user_mobile',auth()->user()->phone_number)
                    ->where('amount','<>',null)->where('status','=',null);;
            })->get();
            $numberof_amount_request = count($all_amount_request);
             
                // $all_request = Amountreq::where('to_user_mobile',auth()->user()->phone_number)->orwhere('from_user_mobile',auth()->user()->phone_number)->get();
              
                    return response()->json(['message'=> 'all amount request',' amount requests'=>$all_amount_request,'count'=>$numberof_amount_request],200);
                } catch (\Throwable $th) {
                    return response()->json(['message'=> 'there is error',' error'=>$th],400);
        
        }
  
        

    }
    public function get_add_request()

    { 
        
        try {
            $all_add_request = Amountreq::where(function ($query)  {
                $query->where('to_user_mobile',auth()->user()->phone_number)
                    ->where('amount','=',null)->where('status','=',null);
            })->get();
            $numberof_add_request = count($all_add_request);

                // $all_request = Amountreq::where('to_user_mobile',auth()->user()->phone_number)->orwhere('from_user_mobile',auth()->user()->phone_number)->get();
              
                    return response()->json(['message'=> 'all add request',' add requests'=>$all_add_request,'count'=>$numberof_add_request],200);
                } catch (\Throwable $th) {
                    return response()->json(['message'=> 'there is error',' error'=>$th],400);
        
        }
  
        

    }

     /**     * @OA\post(
     *      path="/send_request_amount/{id}",
     *      operationId="send notificatio to request money",
     *      tags={"request"},
     *      summary="send notificatio to request money",
     *      description="send notificatio to request money",
     *     @OA\Parameter(
     *          name="id",
     *          description="contact id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *     )
     *
     *  */
    public function send_request_amount(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required',
            'currency' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        try {  
        $user = User::where('id', $id)->first();
        if (!$user) {
            return response()->json(['message' => 'cant found this conatact user'], 400);}
        $amount = $request->post('amount');
        $currency = $request->post('currency');

           
        $contact =Contact::where('id',$user->id)->first();
        

        
        
            if($contact){
 
    event(new RequestNotify($user, $amount, $currency));
    $user_token=User::where('id',$id)->pluck('token_device')->first();
    FCMService::send(
        $token = $user_token,
       [
         'phone'=>auth()->user()->phone_number,
      'country_code'=>auth()->user()->country_code,
     'category'=>"borrowRequest",
    'senderId'=>strval(auth()->user()->id),
  ],
        [
            'title' => ' amount  request',
            'body' => "you have  new amount request",
        ]
    );
    return response()->json(['message' => " notification send"],200);


}else{
    return response()->json(['message' => "contact not found"],400);
}

} 

catch (\Throwable $th) {

    return response()->json(['error' => $th->getMessage()],400);
}

       
    }

   

    /**     * @OA\post(
     *      path="/send_refused_amountrequest/{id}/{req_id}",
     *      operationId="send refused notificatio to request money",
     *      tags={"request"},
     *      summary="send refused notificatio to request money",
     *      description="send refused notificatio to request money",
     *     @OA\Parameter(
     *          name="id",
     *          description="contact id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          ),
     *          ),
     *      @OA\Parameter(
     *          name="req_id",
     *          description="request  id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          ),
     *      ),
     *      
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *     )
     *
     *  */
    public function send_refused_request_amount($id, $req_id)
    {

        $contact = User::where('id', $id)->first();
        $amountreq = Amountreq::where('id', $req_id)->first();
        if($amountreq->status == 'refused'){
            return response()->json(['alert ' => " this request is already refused",'request'=>$amountreq],200);

        }
        try {
            event(new RequestRefusedAmountNotify($contact, $amountreq));
            $user_token=User::where('id',$id)->pluck('token_device')->first();
            FCMService::send(
                $token = $user_token, 
              [
                  'phone'=>auth()->user()->phone_number,
                  'country_code'=>auth()->user()->country_code,
                 'category'=>"responseRequest",
                'senderId'=>strval(auth()->user()->id),
              ],
                 
              
                [
                    'title' => ' amount  request refused',
                    'body' => "your amount request refused  ",
                ]
            );
            return response()->json(['message' => " notification send"],200);

        } catch (\Throwable $th) {
            return response()->json(['message' => " notification not send",'erroe'=>$th],400);
        }
    }


    /**     * @OA\post(
     *      path="/send_confirm_requestamount/{id}/{req_id}",
     *      operationId="send confirm notificatio to request money",
     *      tags={"request"},
     *      summary="send confirm notificatio to request money",
     *      description="send confirm notificatio to request money",
     *     @OA\Parameter(
     *          name="id",
     *          description="contact id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          ),
     *          ),
     *      @OA\Parameter(
     *          name="req_id",
     *          description="request  id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *             ),
     *      
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *     )
     *
     *  */
          public function send_confirm_request_amount($id, $req_id)
       {
        $contact = User::where('id',$id)->first();
        $amountreq = Amountreq::where('id',$req_id)->first();
        if($amountreq->status == 'confirmed'){
            return response()->json(['alert ' => " this request is confirmed",'request'=>$amountreq],200);

        }
        try {
            event(new RequestConfirmAmountNotify($contact, $amountreq));
            $user_token=User::where('id',$id)->pluck('token_device')->first();
            FCMService::send(
               $token = $user_token,
              [
                'phone'=>auth()->user()->phone_number,
                  'country_code'=>auth()->user()->country_code,
                 'category'=>"responseRequest",
                'senderId'=>strval(auth()->user()->id),
              ],
                [
                    'title' => ' amount  request accepted',
                    'body' => "your amount request accepted  ",
                ]
            );
            return response()->json(['message' => " notification send",],200);
           

        } 
        catch (\Throwable $th) {
            return response()->json(['message' => " notification not send",$th],400);
        }
    }




public function currencies()
{
   
 $currencies= DB::table('currencies')->pluck('currency');
 return response()->json(['currencies'=>$currencies],200) ;
}
// public function exchange2()
// {


//     $currencies = ['USD', 'EUR', 'AED'];
//     $data = [];

//     foreach ($currencies as $currency) {
//         $client = new Client();
//         $reqUrl = "http://api.exchangerate.host/convert?access_key=c959934593424bc119294d86ec8f0f45&from=USD&to=EGP&amount=1";

//         try {
//             $response = $client->get($reqUrl);
//             $exchangeRateData = json_decode($response->getBody());
//             dd($exchangeRateData);

//             // Extract the exchange rate value from the response
//             $result = $exchangeRateData->result;         
//             // Add the currency and its exchange rate to the data array
//             $data[$currency] = $result;
//         } catch (\Exception $e) {
//             // Handle HTTP request or JSON parse error...
//             // Log the error or handle it based on your application's requirements
//             // Example: Log::error($e->getMessage());
//         }
//     }
//     foreach ($data as $currency =>$val){
//         DB::table('currencies')->where('currency',$currency)->update(['exchange_rate' =>$val,'updated_at'=> Carbon::now()]);

//     }
// return true;
// }

}
