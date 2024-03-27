<?php

namespace App\Http\Resources\Api\Driver;

use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
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
            'user_name'               => $this->user->user_name,
            'pnr_number'              => $this->pnr_number,
            'address'                 => $this->address,
            'go_date'                 => $this->date_of_journey,
            'back_date'               => $this->back_date,

        ];
    }
}
