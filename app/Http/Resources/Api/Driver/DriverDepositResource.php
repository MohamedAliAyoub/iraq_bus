<?php

namespace App\Http\Resources\Api\Driver;

use Illuminate\Http\Resources\Json\JsonResource;

class DriverDepositResource extends JsonResource
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
            'amount ' => $this->amount,
            'mobile' => $this->mobile,
            'voucher_number' => $this->voucher_number,
            'image' => $this->image_url,
            'status' => $this->status,
            'status_name' => $this->status_name,
            'created_at' => $this->created_at->format('Y-m-d'),
        ];
    }
}
