<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\OpeningHours\OpeningHours;

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
        $parent['is_open'] = $this->when($this->whenLoaded('openingHours'), function () {
            $expected = $this->openingHours->groupBy('day_name')->map(function ($hours) {
                return $hours->map(function ($hour) {
                    return $hour->open.'-'.$hour->close;
                });
            });

            $openingHours = $expected->mapWithKeys(function ($hours, $day) {
                return [strtolower($day) => $hours];
            });

            return OpeningHours::createAndMergeOverlappingRanges($openingHours->toArray())->isOpenAt(Carbon::now());
        });

        return $parent;
    }
}
