<?php

namespace App\Http\Requests\Api\Driver\Auth;

use Illuminate\Foundation\Http\FormRequest;


class ForgetPasswordRequest extends FormRequest
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
       ];
    }
}
