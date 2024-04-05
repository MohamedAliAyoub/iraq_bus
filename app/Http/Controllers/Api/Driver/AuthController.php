<?php

namespace App\Http\Controllers\Api\Driver;

use App\Http\Requests\Driver\DriverStoreRequest;
use App\Models\DriverCarImage;
use App\Models\DriverDetails;
use Illuminate\Http\Request;
use App\Http\Requests\Api\Driver\Auth\{RegisterRequest,
    ResendCodeRequest,
    SendCodeRequest,
    VerifyRequest,
    LoginRequest,
    ForgetPasswordRequest,
    ForgetPasswordCodeRequest,
    ResetPasswordRequest,
    LogoutRequest
};
use App\Http\Requests\Api\Driver\Settings\{ProfileRequest, ChangePasswordRequest};
use App\Http\Controllers\Controller;
use App\Http\Requests\GetLoginCodeRequest;
use App\Http\Resources\Api\Driver\DriverResource;
use App\Mail\SendCode;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->only('profile', 'changePassword');
    }

    /**
     *
     * register for driver only
     *
     * @param RegisterRequest $request
     * @return void
     */
    public function register(RegisterRequest $request)
    {
        $exist = User::where(['mobile' => $request->mobile_code . $request->mobile, 'type' => User::DRIVER])->first();
        if ($exist) {
            $notify[] = ['error', 'The mobile number already exists'];
            return response()->json(['status' => 'fail', 'data' => null, 'message' => 'The mobile number already exists'])->setStatusCode(400);
        }


        $countryData = json_decode(file_get_contents(resource_path('views/partials/country.json')));


        $countryCode = $request->country;
        $user = new User();
        $user->mobile = $request->mobile_code.$request->mobile;
        $user->country_code = $countryCode;
        $user->firstname = $request->firstname;
        $user->username = $request->username;
        $user->lastname = $request->lastname;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->country_code = $request->country_code;
        $user->fleet_type_id = $request->fleet_type_id;
        $user->route_id = $request->route_id;
        $user->address = [
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'zip' => $request->zip,
            'country' => $request->country,
        ];
        $user->status = 0;
        $user->country_code = $request->country_code;
        $user->type = User::DRIVER;
        $user->ev = 1;
        $user->sv = 1;
        $user->save();


        if (!$user->pocket) {
            $user->pocket()->create([
                'debt_balance' => $request->debt_balance,
                'credit_limit' => $request->credit_limit,
            ]);
        }

        try {
            $first_id_card_image_path = uploadImage($request->first_id_card_image, imagePath()['driver']['path']);

            $last_id_card_image_path = uploadImage($request->last_id_card_image, imagePath()['driver']['path']);
            $first_residence_card_image_path = uploadImage($request->first_residence_card_image, imagePath()['driver']['path']);
            $last_residence_card_image_path = uploadImage($request->last_residence_card_image, imagePath()['driver']['path']);
            $first_license_image_path = uploadImage($request->first_license_image, imagePath()['driver']['path']);
            $last_license_image_path = uploadImage($request->last_license_image, imagePath()['driver']['path']);

        } catch (\Exception $exp) {
            return response()->json([
                'status' => 'fail',
                'data' => null,
                'message' => 'Image could not be uploaded.'
            ])->setStatusCode(400);
        }
        if (!$user->driverDetails) {
            $driverDetails = DriverDetails::query()->create([
                'user_id' => $user->id,
                'first_id_card_image' => $first_id_card_image_path,
                'last_id_card_image' => $last_id_card_image_path,
                'first_residence_card_image' => $first_residence_card_image_path,
                'last_residence_card_image' => $last_residence_card_image_path,
                'first_license_image' => $first_license_image_path,
                'last_license_image' => $last_license_image_path,
                'record' => $request->record ,
                'pdf' => $request->pdf ,
                'image' => $request->image,
            ]);
        }
        if (!is_null($request->car_images) && is_array($request->car_images)) {
            foreach ($request->car_images as $image) {
                try {
                    $image_path = uploadImage($image, imagePath()['driver']['path']);
                } catch (\Exception $exp) {

                    return response()->json([
                        'status' => 'fail',
                        'data' => null,
                        'message' => 'Image could not be uploaded.'
                    ])->setStatusCode(400);

                }
//                dd($user, $driverDetails, $image_path);
                DriverCarImage::query()->create([
                    'user_id' => $user->id,
                    'driver_details_id' => $driverDetails->id,
                    'image' => $image_path,
                ]);
            }
        }

        return response()->json(['status' => 'success', 'data' => new DriverResource($user), 'message' => trans('messages.success_register')])->setStatusCode(200);
    }


    /**
     * login
     *
     * @param LoginRequest $request
     * @return void
     */
    public function login(LoginRequest $request)
    {
        $mobile = $request->mobile_code . $request->mobile;
        $credentials = ['mobile' => $mobile, 'password' => $request->password];
        $user = User::firstWhere(['mobile' => $mobile, 'type' => User::DRIVER]);

        if (!Auth::attempt($credentials) || !$user) {
            return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('messages.Wrong_credential')], 401);
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
        return DriverResource::make($user)->additional([
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
        $user = User::where(['mobile' => $request->mobile_code . $request->mobile, 'type' => User::DRIVER])->first();
        if (!$user) {
            return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('messages.not_found')])->setStatusCode(404);
        }
        $code = 1234;
        //   $code = mt_rand(1000, 9999);
        $user->update(['ver_code' => $code]);
        return response()->json(['status' => 'success', 'data' => null, 'message' => trans('messages.success_send_code')])->setStatusCode(200);
    }

    /**
     * resendCode
     *
     * @param ResendCodeRequest $request
     * @return void
     */
    public function resendCode(ResendCodeRequest $request)
    {
        $user = User::where(['mobile' => $request->mobile_code . $request->mobile, 'type' => User::DRIVER])->first();
        if (!$user) {
            return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('messages.not_found')])->setStatusCode(404);
        }
        $code = 1234;
        //   $code = mt_rand(1000, 9999);
        $user->update(['ver_code' => $code]);
        return response()->json(['status' => 'success', 'data' => null, 'message' => trans('messages.success_send_code')])->setStatusCode(200);
    }

    /**
     * Verify
     *
     * @param VerifyRequest $request
     * @return void
     */
    public function verify(VerifyRequest $request)
    {
        $user = User::where(['mobile' => $request->mobile_code . $request->mobile, 'type' => User::DRIVER])->first();
        if ($user && $user->ver_code != $request->code) {
            return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('messages.error_code')])->setStatusCode(400);
        }
        $user->update(['ver_code' => null]);
        return response()->json(['status' => 'success', 'data' => null, 'message' => trans('messages.success_code')])->setStatusCode(200);
    }

    /**
     * Undocumented function
     *
     * @param ResetPasswordRequest $request
     * @return void
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        $user = User::where(['mobile' => $request->mobile_code . $request->mobile, 'type' => User::DRIVER])->first();
        $user = $user->update(['password' => Hash::make($request->password)]);
        return response()->json(['status' => 'success', 'data' => null, 'message' => trans('messages.success_password')])->setStatusCode(200);
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
        $device = \App\Models\UserDevice::where(['user_id' => auth('api')->id(), 'device_token' => $request->device_token, 'type' => $request->type])->first();
        $device != null ? $device->delete() : "";
        auth('api')->logout();
        return response()->json(['status' => 'success', 'data' => null, 'message' => trans('messages.logout_successfully')])->setStatusCode(200);
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
        return response()->json(['status' => 'success', 'data' => new UserResource($user), 'message' => trans('messages.save_profile')])->setStatusCode(200);
    }

    /**
     * changePassword
     *
     * @param ChangePasswordRequest $request
     * @return void
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        $user = auth()->user();
        if (Hash::check($request->old_password, $user->password)) {
            $user->update(['password' => Hash::make($request->password)]);
            return response()->json(['status' => 'success', 'data' => null, 'message' => trans('messages.success_password')])->setStatusCode(200);
        }
        return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('messages.error_old_password')])->setStatusCode(442);
    }
}
