<?php

namespace App\Models;

use Eloquent as Model;

/**
 * Class SettlementManager
 * @package App\Models
 * @version September 25, 2021, 1:46 am UTC
 *
 * @property integer creator_id
 * @property integer restaurant_id
 * @property integer count
 * @property integer delivery_coupons_count
 * @property integer restaurant_coupons_count
 * @property integer restaurant_coupons_on_company_count
 * @property decimal amount
 * @property decimal delivery_coupons_amount
 * @property decimal restaurant_coupons_amount
 * @property decimal restaurant_coupons_on_company_amount
 * @property decimal fee
 * @property string note
 */
class SettlementManager extends Model
{

    public $table = 'settlement_managers';



    public $fillable = [
        'creator_id',
        'restaurant_id',
        'count',
        'delivery_coupons_count',
        'restaurant_coupons_count',
        'restaurant_coupons_on_company_count',
        'amount',
        'delivery_coupons_amount',
        'restaurant_coupons_amount',
        'restaurant_coupons_on_company_amount',
        'fee',
        'note',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'creator_id' => 'integer',
        'restaurant_id' => 'integer',
        'count' => 'integer',
        'delivery_coupons_count' => 'integer',
        'restaurant_coupons_count' => 'integer',
        'restaurant_coupons_on_company_count' => 'integer',
        'amount' => 'float',
        'delivery_coupons_amount' => 'float',
        'restaurant_coupons_amount' => 'float',
        'restaurant_coupons_on_company_amount' => 'float',
        'fee' => 'float',
        'note' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'restaurant_id' => 'required|integer|exists:restaurants,id',
        'note' => 'nullable|string',
    ];

    /**
     * New Attributes
     *
     * @var array
     */
    protected $appends = [
        'custom_fields',
        'total'
    ];


    /**
     * Total value that should company take from manger
     */
    public function getTotalAttribute()
    {
        return $this->amount + $this->delivery_coupons_amount -  $this->restaurant_coupons_on_company_amount;
    }


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
    public function restaurant()
    {
        return $this->belongsTo(\App\Models\Restaurant::class, 'restaurant_id', 'id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'creator_id', 'id');
    }

    /**
     * Get all of the orders for the SettlementDriver
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders()
    {
        return $this->hasMany(\App\Models\Order::class);
    }

    public function loadOrders()
    {
        if ($this->relationLoaded('orders')) return;
        $this->load('orders', 'orders.payment', 'orders.foodOrders', 'orders.restaurantCoupon');
        $this->orders->map(function ($o) {
            $o->amount =  $o->foodOrders->sum(function ($f) {
                return $f->quantity * $f->price;
            });
            if ($o->restaurantCoupon->cost_on_restaurant ?? false) {
                $o->amount -= $o->restaurant_coupon_value;
            }
            $o->fee = round(($this->fee / 100) * $o->amount, 3);
        });
    }
}
