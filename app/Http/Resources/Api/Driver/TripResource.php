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

        $data = [
            'id' => $this->id,
            'date' => $this->date,
            'time' => $this->trip->schedule->start_from,
            'name' => $this->trip->title,
        ];

        if ($this->trip && $this->trip->relationLoaded('bookedTickets')) {
            $data['tickets'] = TicketResource::collection($this->trip->bookedTickets);
        }

        return $data;
    }
}
