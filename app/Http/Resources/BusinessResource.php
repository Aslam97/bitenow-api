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
        $addressFields = [
            'address1' => $this->address1,
            'address2' => $this->address2,
            'city' => $this->city,
            'zip_code' => $this->zip_code,
            'country' => $this->country,
            'state' => $this->state,
            'display_address' => $this->display_address,
        ];

        $this->resource->makeHidden([
            'tags',
            ...array_keys($addressFields),
        ]);

        $parent = parent::toArray($request);

        $parent['cuisines'] = $this->when($this->whenLoaded('cuisines'), function () {
            return $this->cuisines->map(fn ($cuisine) => [
                'name' => $cuisine->name,
                'slug' => $cuisine->slug,
            ]);
        });
        $parent['transactions'] = TransactionResource::collection($this->whenLoaded('tags'));
        $parent['coordinates'] = [
            'latitude' => $this->coordinates->latitude,
            'longitude' => $this->coordinates->longitude,
        ];
        $parent['location'] = $addressFields;
        $parent['rating'] = (float) number_format($this->rating, 2);
        $parent['reviews'] = ReviewResource::collection($this->whenLoaded('reviews'));
        $parent['opening_hours'] = OpeningHourResource::collection($this->whenLoaded('openingHours'));
        $parent['is_closed'] = $this->when($this->whenLoaded('openingHours'), function () {
            $now = now();
            $time = $now->format('H:i:s');

            $openingHours = $this->openingHours->filter(function ($openingHour) {
                return intval($openingHour->day) === day_of_week();
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
        });

        return $parent;
    }
}
