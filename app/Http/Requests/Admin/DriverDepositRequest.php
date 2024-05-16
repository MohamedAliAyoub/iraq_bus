<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;



class DriverDepositRequest extends FormRequest
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
            'gateway_id' => 'required|integer|exists:gateways,id',
            'driver_id' => 'required|integer|exists:users,id',
            'amount' => 'required|numeric',
            'mobile' => 'required|string',
            'voucher_number' => 'required|string',
            'image' => 'required|image',
        ];
    }
    public function messages()
{
    return [
        'gateway_id.required' => 'The gateway ID field is required.',
        'gateway_id.integer' => 'The gateway ID must be an integer.',
        'gateway_id.exists' => 'The selected gateway ID does not exist.',
        'amount.required' => 'The amount field is required.',
        'amount.numeric' => 'The amount must be a number.',
        'mobile.required' => 'The mobile field is required.',
        'mobile.string' => 'The mobile must be a string.',
        'voucher_number.required' => 'The voucher number field is required.',
        'voucher_number.string' => 'The voucher number must be a string.',
        'image.required' => 'The image field is required.',
        'image.image' => 'The image must be an image file.',
        'image.mimes' => 'The image must be a file of type: jpeg, png, bmp, gif, svg, webp.',
    ];
}
}
