<?php

namespace App\Http\Resources\Api\Client;

use Illuminate\Http\Resources\Json\JsonResource;
class ManualGatewayResource extends JsonResource
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
            'name'                          => $this->name ,
            'currency'                      => $this->currency,
            'parameters'                    =>json_decode($this->gateway_parameter),
            'description'                   => $this->method->description,
            'created_at'                    => $this->created_at->format('Y-m-d'),
        ];
    }
}
