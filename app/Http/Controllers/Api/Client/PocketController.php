<?php

namespace App\Http\Controllers\Api\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Client\PocketResource;
use App\Http\Resources\Api\Client\ManualGatewayResource;
use App\Models\GatewayCurrency;
use Illuminate\Support\Facades\Validator;




class PocketController extends Controller
{
    public function __construct()
    {
      $this->middleware('check.user:1,2');
    }
/**
 * get Amount
 * @return PocketResource
 */
public function getAmount()
{
    $user = auth()->user();
    $pocket = $user->pocket;

    return response()->json(['status' => 'success','data'=> PocketResource::make($pocket) ,'message'=>''])->setStatusCode(200);
}

/**
 * get manual gateways
 * @return ManualGatewayResource
 */
public function manualGateways()
{
    $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
      $gate->where('status', 1);
    })->with('method')->orderby('method_code')->get();
    return response()->json(['status' => 'success','data'=> ManualGatewayResource::collection($gatewayCurrency) ,'message'=>''])->setStatusCode(200);
}

/**
 * get Manual Charge
 * @return PocketResource
 */
public function manualCharge(Request $request)
{   $amount = $request->amount;
    $gateway = $request->gateway_id;
    $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
         $gate->where('status', 1);
    })
    ->where('id',$gateway)->first();
    if(!$gatewayCurrency)
    {
      return response()->json(['status' => 'fail','data'=> null ,'message'=>'This gateway is unavailable'])->setStatusCode(400);
    }

    $rules = [];
    $gateway_parameters = json_decode($gatewayCurrency->gateway_parameter,true);
    foreach ($gateway_parameters as $key => $parameter) {
        $fieldName = $parameter['field_name'];
        $validationRules = $parameter['validation'];
        $rules[$fieldName] = $validationRules;
    }
    $validator = Validator::make($request->all(), $rules);
   if ($validator->fails()) {
    return response()->json(['status' => 'fail','message' =>"",
                          'errors' => $validator->errors()->getMessageBag(),
     ], 400);
    } 
    $pocket = auth()->user()->pocket;
    $pocket->increment('amount', $amount);

    return response()->json(['status' => 'success','data'=> PocketResource::make($pocket) ,'message'=>'The charge request sent successfully'])->setStatusCode(200);
}

}

