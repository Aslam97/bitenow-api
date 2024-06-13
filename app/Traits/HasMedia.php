<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

trait HasMedia
{
    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value) => asset($value),
        );
    }

    public function deleteMedia()
    {
        if ($this->image_url && Storage::disk('public')->exists($this->media_url)) {
            Storage::disk('public')->delete($this->image_url);
        }
    }
}
