<?php

namespace App\Models;

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

    protected function casts(): array
    {
        return [
            'open' => 'datetime:H:i',
            'close' => 'datetime:H:i',
        ];
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
