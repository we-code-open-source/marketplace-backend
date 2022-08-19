<?php

namespace App\Models;

use Eloquent as Model;

/**
 * Class DriverWorkTime
 * @package App\Models
 * @version February 28, 2022, 10:08 am EET
 *
 * @property integer user_id
 * @property integer created_by_id
 * @property integer updated_by_id
 * @property string|\Carbon\Carbon from_time
 * @property string|\Carbon\Carbon to_time
 */
class DriverWorkTime extends Model
{

    public $table = 'driver_work_times';



    public $fillable = [
        'user_id',
        'created_by_id',
        'updated_by_id',
        'from_time',
        'to_time'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'user_id' => 'integer',
        'created_by_id' => 'integer',
        'updated_by_id' => 'integer',
        'from_time' => 'datetime',
        'to_time' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'user_id' => 'required|integer|exists:users:id,name',
        'from_time' => 'required',
        'to_time' => 'required'
    ];

    /**
     * New Attributes
     *
     * @var array
     */
    protected $appends = [
        //  'custom_fields',
        'time',

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


    public function getTimeAttribute()
    {
        if ($this->to_time) {
            return $this->to_time->diffInSeconds($this->from_time);
        }
        return '0';
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
    public function createdBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function updatedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by_id', 'id');
    }
}
