<?php

namespace App\Http\Resources\Api\Client;

use Illuminate\Http\Resources\Json\JsonResource;

class FleetTypeResource extends JsonResource
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
            'id'                            => $this->fleetType->id,
            'name'                          => $this->fleetType->name ,
            'seat_layout'                   => $this->fleetType->seat_layout,
            'number_decks'                  => array_sum($this->fleetType->deck_seats),
            'facilities'                    => $this->fleetType->facilities,
            'amount'                        => $this->price,
            'created_at'                    => $this->fleetType->created_at->format('Y-m-d'),
        ];
    }
}
