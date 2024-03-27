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
            'username'         =>'required|string',
            'country_code'     =>'required|string',
            'mobile_code'      =>'required|numeric',
            'mobile'           =>'required|numeric|digits_between:10,16',
            'fleet_type_id'    =>'required|exists:fleet_types,id',
            'route_id'         =>'required|exists:vehicle_routes,id',
            'password'         =>'required|confirmed|min:8|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/|regex:/[@$!%*#?&]/',
        ];
    }
}
