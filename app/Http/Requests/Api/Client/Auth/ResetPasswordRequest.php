<?php

namespace App\Http\Requests\Api\Client\Auth;

use Illuminate\Foundation\Http\FormRequest;


class ResetPasswordRequest extends FormRequest
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
            'password'         =>'required|confirmed|min:8|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/|regex:/[@$!%*#?&]/',
            'type'             => 'required|in:1,2'

       ];
    }
}
