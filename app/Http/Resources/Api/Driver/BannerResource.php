<?php

namespace App\Http\Resources\Api\Driver;

use Illuminate\Http\Resources\Json\JsonResource;

class BannerResource extends JsonResource
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
            'description '                  => $this->description ,
            'link'                          => $this->link,
            'publish_date'                  => $this->publish_date,
            'image'                         => asset('assets/images/banner/'.$this->image),
            'created_at'                    => $this->created_at->format('Y-m-d'),
        ];
    }
}
