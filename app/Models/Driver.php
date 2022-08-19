<?php

namespace App\Models;

use Eloquent as Model;
use App\Events\CreatedDriverEvent;
use App\Events\UpdatedDriverEvent;

/**
 * Class Driver
 * @package App\Models
 * @version March 25, 2020, 9:47 am UTC
 *
 * @property \App\Models\User user
 * @property integer user_id
 * @property double delivery_fee
 * @property enum type
 * @property integer total_orders
 * @property double earning
 * @property boolean available
 */
class Driver extends Model
{

    public $table = 'drivers';
    public $primaryKey = 'id';



    public $fillable = [
        'user_id',
        'driver_type_id',
        'delivery_fee',
        'type',
        'total_orders',
        'earning',
        'available',
        'working_on_order'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'user_id' => 'integer',
        'driver_type_id' => 'integer',
        'delivery_fee' => 'double',
        'type' => 'string',
        'total_orders' => 'integer',
        'earning' => 'double',
        'available' => 'boolean',
        'working_on_order' => 'boolean'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'delivery_fee' => 'required',
        'type' => 'required|in:bicycle,motorcycle,car',
        'driver_type_id' => 'required|integer|exists:driver_types,id',
        //'user_id' => 'required|exists:users,id'
    ];

    /**
     * New Attributes
     *
     * @var array
     */
    protected $appends = [
        'custom_fields',
        'driverType'
    ];

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => CreatedDriverEvent::class,
        'updated' => UpdatedDriverEvent::class,
    ];



    /**
     * @var array
     */
    private $drivers_types = [
        'bicycle' => 'Bicycle',
        'motorcycle' => 'Motorcycle',
        'car' => 'Car'
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
     * get driverType attribute
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Relations\BelongsTo|object|null
     */
    public function getDriverTypeAttribute()
    {
        return $this->driverType()->first(['id', 'name', 'range', 'last_access']);
    }

    /**
     * user is driver 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function driverType()
    {
        return $this->belongsTo(\App\Models\DriverType::class, 'driver_type_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function driverWorkTime()
    {
        return $this->hasMany(\App\Models\DriverWorkTime::class, 'user_id', 'user_id');
    }

    public function types()
    {
        return $this->drivers_types;
    }
}
