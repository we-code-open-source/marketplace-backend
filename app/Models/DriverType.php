<?php

namespace App\Models;

use Eloquent as Model;

/**
 * Class DriverType
 * @package App\Models
 * @version September 6, 2021, 10:37 am UTC
 *
 * @property string name
 * @property decimal range
 * @property integer last_access
 */
class DriverType extends Model
{

    public $table = 'driver_types';



    public $fillable = [
        'name',
        'range',
        'last_access'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'name' => 'string',
        'range' => 'float',
        'last_access' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required|string|min:3|max:32|unique:driver_types,name',
        'range' => 'required|numeric|min:1',
        'last_access' => 'required|integer|min:1'
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
}
