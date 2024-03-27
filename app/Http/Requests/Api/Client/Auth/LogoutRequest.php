<?php

namespace App\Http\Requests\Api\Client\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LogoutRequest extends FormRequest
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
            'device_token'   => 'required|exists:user_devices,device_token',
            'type'           => 'required|exists:user_devices,type',
        ];
    }

}
