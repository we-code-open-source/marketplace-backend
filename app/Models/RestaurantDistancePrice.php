<?php

namespace App\Models;

use App\Traits\IsAvailable;
use Illuminate\Database\Eloquent\Model;

class RestaurantDistancePrice extends Model
{
    use IsAvailable;

    protected $table = "restaurant_distance_prices";

    protected $fillable = [
        'price',
        'restaurant_id',
        'is_available',
        'from',
        'to'
    ];

    public static $rules = [
        'price' => 'required',
        'restaurant_id' => 'required|exists:restaurants,id',
        'from' => 'numeric|required|max:200|min:0',
        'to' => 'numeric|required|max:200|gt:from'
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

}
