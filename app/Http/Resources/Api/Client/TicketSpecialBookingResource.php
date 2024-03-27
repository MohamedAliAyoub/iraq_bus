<?php

namespace App\Http\Resources\Api\Client;

use Illuminate\Http\Resources\Json\JsonResource;

class TicketSpecialBookingResource extends JsonResource
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
            'user'                    => $this->responsible_name ? $this->responsible_name :$this->user->username,
            'sub_total'               => $this->sub_total,
            'pnr_number'              => $this->pnr_number,
            'status'                  => $this->status,
            // 'trip_id'                 => $this->trip_id,
            // 'source_destination'      => $this->source_destination,
            // 'pickup_point'            => $this->pickup_point,
            // 'dropping_point'          => $this->dropping_point,
            // 'seats'                   => $this->seats,
            // 'ticket_count'            => $this->ticket_count,
            // 'unit_price'              => $this->unit_price,
            // 'go_date'                 => $this->go_date,
            // 'back_date'               => $this->back_date,
        ];
    }
}
