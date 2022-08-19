<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Distance extends Model
{

    public $fillable = [
        'from_latitude',
        'from_longitude',
        'to_latitude',
        'to_longitude',
        'distance_value',
        'duration_value',
        'distance_text',
        'duration_text',
        'response',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'from_latitude' => 'float',
        'from_longitude' => 'float',
        'to_latitude' => 'float',
        'to_longitude' => 'float',
        'distance_value' => 'float',
        'duration_value' => 'integer',
        'distance_text' => 'string',
        'duration_text' => 'string',
    ];


    /**
     * Return distance with duration only as array
     * 
     * @return array
     */
    public function getDistance()
    {
        return [
            'distance' => ['text' => $this->distance_text, 'value' => $this->distance_value],
            'duration' => ['text' => $this->duration_text, 'value' => $this->duration_value],
        ];
    }
}
