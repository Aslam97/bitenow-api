<?php

namespace App\Models;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Traits\HasSpatial;
use Propaganistas\LaravelPhone\PhoneNumber;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Tags\HasTags;

class Business extends Model
{
    use HasFactory;
    use HasSlug;
    use HasSpatial;
    use HasTags;

    protected $fillable = [
        'name',
        'slug',
        'image_url',
        'url',
        'phone',
        'phone_country_code',
        'price',

        // Address
        'address1',
        'address2',
        'city',
        'state',
        'zip_code',
        'country',
        'coordinates',
    ];

    protected $casts = [
        'phone' => 'string',
        'phone_country_code' => 'string',
        'price' => 'integer',
        'coordinates' => Point::class,
    ];

    protected $hidden = [
        'phone_country_code',
    ];

    protected $appends = [
        'display_phone',
        'display_address',
        'price_display',
    ];

    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function openingHours()
    {
        return $this->hasMany(OpeningHour::class);
    }

    public function cuisines()
    {
        return $this->belongsToMany(Cuisine::class);
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    protected function displayPhone(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => (new PhoneNumber($attributes['phone'], $attributes['phone_country_code']))
                ->formatNational()
        );
    }

    protected function priceDisplay(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => str_repeat('$', $attributes['price'])
        );
    }

    protected function displayAddress(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => implode(', ', array_filter([
                $attributes['address1'],
                $attributes['address2'],
                $attributes['city'],
                $attributes['state'],
                $attributes['zip_code'],
                $attributes['country'],
            ]))
        );
    }

    public function scopeSearchAddress(Builder $query, string $address): Builder
    {
        return $query->where('address1', 'like', "%$address%")
            ->orWhere('address2', 'like', "%$address%")
            ->orWhere('city', 'like', "%$address%")
            ->orWhere('state', 'like', "%$address%")
            ->orWhere('zip_code', 'like', "%$address%")
            ->orWhere('country', 'like', "%$address%");
    }
}
