<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpeningHour extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'day',
        'open',
        'close',
    ];

    protected $appends = [
        'day_name',
    ];

    protected function open(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => now()->setTimeFromTimeString($attributes['open'])->format('H:i'),
        );
    }

    protected function close(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => now()->setTimeFromTimeString($attributes['close'])->format('H:i'),
        );
    }

    protected function getDayNameAttribute()
    {
        $days = [
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
            7 => 'Sunday',
        ];

        return $days[$this->day];
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
