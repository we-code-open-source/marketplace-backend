<?php

namespace App\Models;

use Eloquent as Model;

/**
 * Class DriverReview
 * @package App\Models
 * @version October 1, 2021, 2:26 pm UTC
 *
 * @property string review
 * @property integer rate
 * @property integer user_id
 * @property integer driver_id
 */
class DriverReview extends Model
{

    public $table = 'driver_reviews';



    public $fillable = [
        'review',
        'rate',
        'user_id',
        'driver_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'review' => 'string',
        'rate' => 'integer',
        'user_id' => 'integer',
        'driver_id' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'review' => 'required|string',
        'rate' => 'required|integer|min:1|max:5',
        'user_id' => 'required|integer|exists:users,id',
        'driver_id' => 'required|integer|exists:users,id'
    ];

    /**
     * New Attributes
     *
     * @var array
     */
    protected $appends = [
        'custom_fields',

    ];

    public function customFieldsValues()
    {
        return $this->morphMany('App\Models\CustomFieldValue', 'customizable');
    }

    public function getCustomFieldsAttribute()
    {
        $hasCustomField = in_array(static::class, setting('custom_field_models', []));
        if (!$hasCustomField) {
            return [];
        }
        $array = $this->customFieldsValues()
            ->join('custom_fields', 'custom_fields.id', '=', 'custom_field_values.custom_field_id')
            ->where('custom_fields.in_table', '=', true)
            ->get()->toArray();

        return convertToAssoc($array, 'name');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function driver()
    {
        return $this->belongsTo(\App\Models\User::class, 'driver_id', 'id');
    }
}
