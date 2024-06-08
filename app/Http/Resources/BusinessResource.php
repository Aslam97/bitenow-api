<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BusinessResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'image_url' => $this->image_url,
            'url' => $this->url,
            'phone' => $this->phone,
            'display_phone' => $this->display_phone,
            'categories' => CategoryResource::collection($this->whenLoaded('tags')),
            'coordinates' => [
                'latitude' => $this->coordinates->latitude,
                'longitude' => $this->coordinates->longitude,
            ],
            'location' => [
                'address1' => $this->address1,
                'address2' => $this->address2,
                'city' => $this->city,
                'zip_code' => $this->zip_code,
                'country' => $this->country,
                'state' => $this->state,
                'display_address' => $this->display_address,
            ],
            'rating' => (float) number_format($this->rating, 2),
            'distance' => $this->distance,
            'reviews' => ReviewResource::collection($this->whenLoaded('reviews')),
        ];
    }
}
