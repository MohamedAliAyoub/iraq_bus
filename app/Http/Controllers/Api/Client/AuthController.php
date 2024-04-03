<?php

namespace App\Http\Controllers\Api\Client;

use Illuminate\Http\Request;
use App\Http\Requests\Api\Client\Auth\{ RegisterRequest , ResendCodeRequest , SendCodeRequest ,VerifyRequest ,LoginRequest ,ForgetPasswordRequest ,ForgetPasswordCodeRequest ,ResetPasswordRequest,LogoutRequest };
use App\Http\Requests\Api\Client\Profile\{ProfileRequest ,ChangePasswordRequest};
use App\Http\Controllers\Controller;
use App\Http\Requests\GetLoginCodeRequest;
use App\Http\Resources\Api\Client\ClientResource;
use App\Mail\SendCode;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->only('profile');
    }

    /**
     *
     * register for client only
     *
     * @param RegisterRequest $request
     * @return void
     */
    public function register(RegisterRequest $request)
    {
        $exist = User::where(['mobile'=> $request->mobile_code.$request->mobile,'type'=> User::CLIENT])->first();
        if ($exist) {
            $notify[] = ['error', 'The mobile number already exists'];
            return response()->json(['status' => 'fail','data'=> null ,'message'=>'The mobile number already exists'])->setStatusCode(400);
        }
        $allRequest = $request->validated();
        $user = User::create(collect($allRequest)->except(['password','mobile','address'])->toArray());

        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->mobile = $request->mobile_code.$request->mobile;
        $user->address = [
            'address' => $request->address['address'] ?? null ,
            'state' => $request->address['state'] ?? null,
            'zip' => $request->address['zip'] ?? null,
            'country' =>$request->address['country'] ?? null ,
            'city' => $request->address['city'] ?? null
        ];
        $user->status = 1;
        $user->type = User::CLIENT;
        $user->ev =  1;
        $user->sv =  1;
        $user->save();

        return response()->json(['status' => 'success','data'=> new ClientResource($user) ,'message'=>trans('messages.success_register')])->setStatusCode(200);
    }




    /**
     * login
     *
     * @param LoginRequest $request
     * @return void
     */
    public function login(LoginRequest $request)
    {
      $mobile = $request->mobile_code.$request->mobile;
      $credentials = ['mobile' => $mobile ,'password'=>$request->password];
     $user = User::firstWhere(['mobile' => $mobile,'type'=> $request->type]);
      if (!Auth::attempt($credentials) || !$user) {
        return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('messages.Wrong_credential')],401);
      }

      $user->devices()->where('device_token', "<>", $request->device_token)->delete();
      $user->devices()->firstOrCreate($request->only(['device_token', 'device_type']));


      $token = $user->createToken('mobile')->plainTextToken;
      data_set($user, 'token', $token);
      if (!$user->pocket) {
        $user->pocket()->create([
            'amount' => 0 
        ]);
      }
      return ClientResource::make($user)->additional([
        'status' => 'success',
        'message' => trans('auth.success_login'),
       ]);

    }
      /**
   * forget Password
   *
   * @param ForgetPasswordRequest $request
   * @return void
   */
  public function forgetPassword(ForgetPasswordRequest $request)
  {
      $user = User::where(['mobile'=> $request->mobile_code.$request->mobile,'type'=>  $request->type])->first();
      if (!$user) {
        return response()->json(['status' => 'fail','data'=> null ,'message'=>trans('messages.not_found')])->setStatusCode(404);
      }
      $code = 1234 ;
     //   $code = mt_rand(1000, 9999);
      $user->update(['ver_code' => $code]);
     return response()->json(['status' => 'success','data'=> null ,'message'=>trans('messages.success_send_code')])->setStatusCode(200);
  }
      /**
     * resendCode
     *
     * @param ResendCodeRequest $request
     * @return void
     */
    public function resendCode(ResendCodeRequest $request)
    {
        $user = User::where(['mobile'=> $request->mobile_code.$request->mobile,'type'=>  $request->type])->first();
        if (!$user) {
            return response()->json(['status' => 'fail','data'=> null ,'message'=>trans('messages.not_found')])->setStatusCode(404);
          }
        $code = 1234 ;
       //   $code = mt_rand(1000, 9999);
        $user->update(['ver_code' => $code]);
       return response()->json(['status' => 'success','data'=> null ,'message'=>trans('messages.success_send_code')])->setStatusCode(200);
    }
  /**
   * Verify
   *
   * @param VerifyRequest $request
   * @return void
   */
    public function verify(VerifyRequest $request)
    {
        $user = User::where(['mobile'=> $request->mobile_code.$request->mobile,'type'=>  $request->type])->first();
      if ($user && $user->ver_code != $request->code) {
        return response()->json(['status' => 'fail','data'=> null ,'message'=>trans('messages.error_code')])->setStatusCode(400);
      }
      $user->update(['ver_code' =>null ]);
      return response()->json(['status' => 'success','data'=> null ,'message'=>trans('messages.success_code')])->setStatusCode(200);
    }

    /**
     * Undocumented function
     *
     * @param ResetPasswordRequest $request
     * @return void
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
      $user = User::where(['mobile'=> $request->mobile_code.$request->mobile,'type'=>  $request->type])->first();
      $user = $user->update(['password' => Hash::make($request->password)]);
      return response()->json(['status' => 'success','data'=> null ,'message'=>trans('messages.success_password')])->setStatusCode(200);
    }
    /**
     * Undocumented function
     *
     * @param LogoutRequest $request
     * @return void
     */
    public function logout(LogoutRequest $request)
     {
        $user = auth('api')->user();
        $device = \App\Models\UserDevice::where(['user_id'=>auth('api')->id(),'device_token' => $request->device_token , 'type' => $request->type ])->first();
        $device !=null ? $device->delete() :"";
        auth('api')->logout();
        return response()->json(['status' => 'success','data'=> null ,'message'=> trans('messages.logout_successfully')])->setStatusCode(200);
     }
     /**
      * Undocumented function
      *
      * @param ProfileRequest $request
      * @return void
      */
     public function profile(ProfileRequest $request)
     {
        $user = auth('api')->user();
        $user->update($request->validated());
        return response()->json(['status' => 'success','data'=> new UserResource($user) ,'message'=> trans('messages.save_profile')])->setStatusCode(200);
     }

     /**
      * changePassword
      *
      * @param ChangePasswordRequest $request
      * @return void
      */
     public function changePassword(ChangePasswordRequest $request)
     {
        $user = auth('api')->user();
        if (Hash::check($request->old_password,  $user->password)) {
          $user->update(['password' => $request->password]);
          return response()->json(['status' => 'success','data'=> null ,'message'=> trans('messages.success_password')])->setStatusCode(200);
        }
        return response()->json(['status' => 'fail','data'=> null ,'message'=>trans('messages.error_old_password')])->setStatusCode(442);
     }
}
