<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\FileTypeValidate;



class BannerRequest extends FormRequest
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
            'description'      =>'required',
            'image'            => ['image',new FileTypeValidate(['jpg','jpeg','png'])],
            'link'             =>'nullable',
            'publish_date'     =>'required|date',
       ];
    }
}
