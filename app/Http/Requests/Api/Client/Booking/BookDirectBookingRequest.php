<?php

namespace App\Http\Requests\Api\Client\Booking;

use Illuminate\Foundation\Http\FormRequest;


class BookDirectBookingRequest extends FormRequest
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
            'trip_id'              => ['required','exists:trips,id'],
            'type'                 =>'nullable|in:go,back',
            'go_date'              =>'required|date_format:Y-m-d|after_or_equal:today',
            'back_date'            =>'required_if:type,back|date_format:Y-m-d|after_or_equal:today',
            'seats'                => ['required', 'array'],
            'seats.*.id'           => ['required', 'numeric'],
            'seats.*.name'         => ['required', 'string'],
            'seats.*.gender'       => ['required', 'string'],
            'seats.*.client_name'  => ['required', 'string'],
            'seats.*.client_phone' => ['required', 'string'],
        ];
    }

}
