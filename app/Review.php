<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    /**
     * The attributes dates.
     *
     * @var array
     */
    protected $dates = [
        'attended',
    ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'restaurant', 'author', 'attended', 'review', 'rating', 'created_at', 'web'
    ];
}
