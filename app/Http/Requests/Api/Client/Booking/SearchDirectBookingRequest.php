<?php

namespace App\Http\Requests\Api\Client\Booking;

use Illuminate\Foundation\Http\FormRequest;


class SearchDirectBookingRequest extends FormRequest
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
            'go_date'          =>'required|date_format:Y-m-d|after_or_equal:today',
            'back_date'        =>'required_if:type,back|date_format:Y-m-d|after_or_equal:today',

        ];
    }



    public function messages()
   {
        return [
            'pickup.different'        => 'Please select pickup point and destination point properly',
            'go_date.after_or_equal'  => 'Date of journey can\'t be less than today.',
            'back_date.after_or_equal'=> 'Date of journey can\'t be less than today or Go Date.',

        ];
   }
}
