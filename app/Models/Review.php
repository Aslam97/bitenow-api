<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'rating',
        'comment',
        'helpful',
        'thanks',
        'love_this',
        'oh_no',
    ];

    public function reviewable()
    {
        return $this->morphTo();
    }

    public function author()
    {
        return $this->morphTo('author');
    }
}
