<?php

namespace App\Http\Resources\Api\Client;

use Illuminate\Http\Resources\Json\JsonResource;

class HistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'route' => $this->route,
            'passenger_numbers' => $this->bookedTicket->passenger_numbers,
            'passenger_phone' => $this->bookedTicket->responsible_phone,
            'passenger_name' => $this->bookedTicket->responsible_name,
            'amount' => $this->amount,
            'creditor' => $this->creditor,
            'debtor' => $this->debtor,
            'total' => $this->total,
            'type' => $this->type,
            'created_at' => $this->created_at->format('Y-m-d'),
        ];
    }
}
