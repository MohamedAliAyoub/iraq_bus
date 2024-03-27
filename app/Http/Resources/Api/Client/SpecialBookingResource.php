<?php

namespace App\Http\Resources\Api\Client;

use Illuminate\Http\Resources\Json\JsonResource;
class SpecialBookingResource extends JsonResource
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
            'title '                        => $this->title ,
            'seat_layout'                   => $this->fleetType->seat_layout,
            'fleet_type'                    => $this->fleetType->name,
            'start_date'                    => showDateTime($this->schedule->start_from,'h:i A'),
            'end_date'                      => showDateTime($this->schedule->end_at,'h:i A'),
            'pickup'                        => $this->startFrom->name,
            'destination'                   => $this->endTo->name,
            'amount'                        => $this->price,
            'facilities'                    => $this->fleetType->facilities,
            'created_at'                    => $this->created_at->format('Y-m-d'),

        ];
    }
}
