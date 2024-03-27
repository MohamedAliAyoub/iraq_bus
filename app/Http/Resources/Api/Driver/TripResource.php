<?php

namespace App\Http\Resources\Api\Driver;

use Illuminate\Http\Resources\Json\JsonResource;

class TripResource extends JsonResource
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
            'id'                      => $this->id,
            'name'                    => $this->title,
            'tickets'                 => TicketResource::collection($this->whenLoaded('bookedTickets')), 
        ];
    }
}
