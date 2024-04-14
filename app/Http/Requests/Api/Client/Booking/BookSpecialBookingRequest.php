<?php

namespace App\Http\Requests\Api\Client\Booking;

use Illuminate\Foundation\Http\FormRequest;


class BookSpecialBookingRequest extends FormRequest
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
        $rules = [
            'destination'          =>'required',
            'pickup'               => ['different:destination', 'required'],
            'type'                 =>'nullable|in:go,back',
            'go_date'              =>'required|date_format:Y-m-d|after_or_equal:today',
            'back_date'            =>'required_if:type,back|date_format:Y-m-d|after_or_equal:today',
            'fleet_type'           =>'required|exists:fleet_types,id',
            'responsible_name'     =>'required',
            'responsible_phone'    =>'required',
            'passenger_numbers'    => ['required', 'numeric'],
//            'seats'                => ['required', 'array'],
//            'seats.*.gender'       => ['required', 'string'],
//            'seats.*.client_name'  => ['required', 'string'],
//            'seats.*.client_phone' => ['required', 'string'],
        ];

        if (auth()->user()->type == 2) {
            $rules['government_id'] = 'required';
            $rules['city_id'] = 'required';
            $rules['address'] = 'required';
        }
        return $rules;
    }

}
