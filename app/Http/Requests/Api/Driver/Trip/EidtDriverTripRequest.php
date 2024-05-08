<?php

namespace App\Http\Requests\Api\Driver\Trip;

use Illuminate\Foundation\Http\FormRequest;

class EidtDriverTripRequest extends FormRequest
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
            'driver_id' => ['required', 'exists:users,id,type,3'],
            'route_id' => ['nullable', 'exists:vehicle_routes,id,status,1'],
            'schedule_id' => ['nullable', 'exists:schedules,id,status,1'],
            'day_off' => ['nullable', 'array'],
            'day_off.*' => ['nullable', 'string'],
        ];
    }
}
