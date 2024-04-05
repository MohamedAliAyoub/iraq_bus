<?php

namespace App\Http\Requests\Api\Driver\Auth;

use Illuminate\Foundation\Http\FormRequest;


class RegisterRequest extends FormRequest
{
    /**
     * Determine if the client is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        return [
            'first_name'         =>'nullable|string',
            'last_name'         =>'nullable|string',
            'username'         =>'required|string',
            'email' => 'nullable|email|max:90|unique:users,email',
            'country_code'     =>'required|string',
            'mobile_code'      =>'required|numeric',
            'mobile'           =>'required|numeric|digits_between:10,16',
            'fleet_type_id'    =>'required|exists:fleet_types,id',
            'route_id'         =>'required|exists:vehicle_routes,id',
            'password'         =>'required|confirmed|min:8|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/|regex:/[@$!%*#?&]/',
            'first_id_card_image' => 'required|image',
            'last_id_card_image' => 'required|image',
            'first_residence_card_image' => 'required|image',
            'last_residence_card_image' => 'required|image',
            'first_license_image' => 'required|image',
            'last_license_image' => 'required|image',
            'record' => 'nullable|mimes:mp3',
            'pdf' => 'nullable|mimes:pdf',
            'image' => 'nullable|image',
            'car_images.*' => 'required|image', // Ensure each element is an image
            'car_images' => 'required|array|size:4', // Ensure exactly 4 images are uploaded
            ];
    }
}
