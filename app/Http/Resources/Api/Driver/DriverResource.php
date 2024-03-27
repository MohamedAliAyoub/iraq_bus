<?php

namespace App\Http\Resources\Api\Driver;

use Illuminate\Http\Resources\Json\JsonResource;

class DriverResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'                            => $this->id,
            'username '                     => $this->username ,
            'mobile'                        => $this->mobile,
            'country_code'                  => $this->country_code,
            'fleet_type'                    => $this->fleetType->name,
            'route'                         => $this->route->name,
            'status'                        => $this->status,
            'type'                          => $this->type,
            'token'                         => $this->when($this->token,$this->token),

        ];
    }
}
