<?php

namespace App\Http\Requests\Driver;

use Illuminate\Foundation\Http\FormRequest;

class DriverStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
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
            'firstname' => 'required|max:50',
            'username' => 'required|max:50',
            'lastname' => 'required|max:50',
            'email' => 'required|email|max:90|unique:users,email',
            'mobile' => 'required|unique:users,mobile',
            'address.*' => 'required',
            'password' =>  'required|confirmed|min:8|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/|regex:/[@$!%*#?&]/',
            'first_id_card_image' => 'required|image',
            'last_id_card_image' => 'required|image',
            'first_residence_card_image' => 'required|image',
            'last_residence_card_image' => 'required|image',
            'first_license_image' => 'required|image',
            'last_license_image' => 'required|image',
            'record' => 'nullable|mimes:mp3',
            'pdf' => 'nullable|mimes:pdf',
            'image' => 'nullable|image',
            'car_images.*' => 'required|image', // Ensure each element is an image
            'car_images' => 'required|array|size:4', // Ensure exactly 4 images are uploaded
        ];
    }
}
