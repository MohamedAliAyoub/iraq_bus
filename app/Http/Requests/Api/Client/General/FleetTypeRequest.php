<?php

namespace App\Http\Requests\Api\Client\General;

use Illuminate\Foundation\Http\FormRequest;


class FleetTypeRequest extends FormRequest
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
            'pickup'           =>'required',
            'destination'      =>'required',
            'pickup'           => ['different:destination', 'required'],
            'type'             =>'nullable|in:go,back',
        ];
    }



    public function messages()
   {
        return [
            'pickup.different'        => 'Please select pickup point and destination point properly',
        ];
   }
}
