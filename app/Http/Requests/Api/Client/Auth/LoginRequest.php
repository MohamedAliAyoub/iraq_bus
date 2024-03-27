<?php

namespace App\Http\Requests\Api\Client\Auth;

use Illuminate\Foundation\Http\FormRequest;


class LoginRequest extends FormRequest
{
    /**
     * Determine if the Client is authorized to make this request.
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
            'mobile_code'      =>'required|numeric',
            'mobile'           =>'required|numeric|digits_between:10,16',
            'password'         =>'required|min:8|max:20',
            'device_token'     =>'required',
            'device_type'      =>'required|in:ios,android',
            'type'             => 'required|in:1,2'

       ];
    }
}
