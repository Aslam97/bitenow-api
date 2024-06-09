<?php

namespace App\Http\Resources;

use Carbon\Carbon;
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
        $parent = [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'image_url' => $this->image_url,
            'url' => $this->url,
            'phone' => $this->phone,
            'display_phone' => $this->display_phone,
            'price' => $this->price,
            'price_display' => $this->price_display,
            'cuisines' => $this->when($this->whenLoaded('cuisines'), function () {
                return $this->cuisines->map(fn ($cuisine) => [
                    'name' => $cuisine->name,
                    'slug' => $cuisine->slug,
                ]);
            }),
            'transaction' => TransactionResource::collection($this->whenLoaded('tags')),
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
            'review_count' => $this->reviews_count,
            'distance' => $this->distance,
            'reviews' => ReviewResource::collection($this->whenLoaded('reviews')),
            'opening_hours' => OpeningHourResource::collection($this->whenLoaded('openingHours')),
            'is_closed' => $this->when($this->whenLoaded('openingHours'), function () {
                $now = now();
                $day = $now->format('l');
                $time = $now->format('H:i:s');

                $openingHours = $this->openingHours->filter(function ($openingHour) use ($day) {
                    return  intval($openingHour->day) === Carbon::parse($day)->dayOfWeek;
                });

                if ($openingHours->isEmpty()) {
                    return true;
                }

                $isOpen = $openingHours->filter(function ($openingHour) use ($time) {
                    $open = Carbon::parse($openingHour->open);
                    $close = Carbon::parse($openingHour->close);

                    return $open->lte($time) && $close->gte($time);
                });

                return $isOpen->isEmpty();
            }),
        ];

        return $parent;
    }
}
