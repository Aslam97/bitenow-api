<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
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
            'rating' => $this->rating,
            'comment' => $this->comment,
            'helpful' => $this->helpful,
            'thanks' => $this->thanks,
            'love_this' => $this->love_this,
            'oh_no' => $this->oh_no,
            'author' => UserResource::make($this->whenLoaded('author')),
        ];
    }
}
