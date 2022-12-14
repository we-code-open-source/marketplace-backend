<?php

/**
 * File name: Restaurant.php
 * Last modified: 2020.04.30 at 08:21:09
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2020
 *
 */

namespace App\Models;

use Carbon\Carbon;
use Eloquent as Model;
use App\Traits\SkipAppends;
use Spatie\Image\Manipulations;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\Models\Media;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

/**
 * Class Restaurant
 * @package App\Models
 * @version August 29, 2019, 9:38 pm UTC
 *
 * @property \Illuminate\Database\Eloquent\Collection Food
 * @property \Illuminate\Database\Eloquent\Collection Gallery
 * @property \Illuminate\Database\Eloquent\Collection RestaurantsReview
 * @property \Illuminate\Database\Eloquent\Collection[] discountables
 * @property \Illuminate\Database\Eloquent\Collection[] cuisines
 * @property \Illuminate\Database\Eloquent\Collection[] User
 * @property \Illuminate\Database\Eloquent\Collection[] Restaurant
 * @property string name
 * @property string description
 * @property string address
 * @property string latitude
 * @property string longitude
 * @property string phone
 * @property string mobile
 * @property string information
 * @property double admin_commission
 * @property double delivery_fee
 * @property double default_tax
 * @property double delivery_range
 * @property boolean available_for_delivery
 * @property enum delivery_price_type
 * @property boolean closed
 * @property boolean active
 * @property boolean featured
 * @property boolean is_restaurant
 */
class Restaurant extends Model implements HasMedia
{
    use HasMediaTrait {
        getFirstMediaUrl as protected getFirstMediaUrlTrait;
    }

    use SkipAppends;

    public $table = 'restaurants';



    public $fillable = [
        'name',
        'description',
        'address',
        'latitude',
        'longitude',
        'phone',
        'mobile',
        'admin_commission',
        'delivery_fee',
        'default_tax',
        'delivery_range',
        'delivery_price_type',
        'available_for_delivery',
        'closed',
        'information',
        'active',
        'private_drivers',
        'featured',
        'is_restaurant',
        'close_at',
        'open_at'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'name' => 'string',
        'description' => 'string',
        'image' => 'string',
        'address' => 'string',
        'latitude' => 'string',
        'longitude' => 'string',
        'phone' => 'string',
        'mobile' => 'string',
        'admin_commission' => 'double',
        'delivery_fee' => 'double',
        'default_tax' => 'double',
        'delivery_range' => 'double',
        'available_for_delivery' => 'boolean',
        'closed' => 'boolean',
        'information' => 'string',
        'active' => 'boolean',
        'private_drivers' => 'boolean',
        'featured' => 'boolean',
        'is_restaurant' => 'boolean'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $adminRules = [
        'name' => 'required',
        //'description' => 'required',
        'close_at' => 'required',
        'open_at' => 'required',
        'phone' => 'required',
        'delivery_fee' => 'nullable|numeric|min:0',
        'longitude' => 'required|numeric',
        'latitude' => 'required|numeric',
        // 'admin_commission' => 'required|numeric|min:0',
        // 'private_drivers' => 'required|boolean'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $managerRules = [
        'name' => 'required',
        //'description' => 'required',
        'close_at' => 'required',
        'open_at' => 'required',
        'delivery_fee' => 'nullable|numeric|min:0',
        'longitude' => 'required|numeric',
        'latitude' => 'required|numeric',
        // 'private_drivers' => 'required|boolean'
    ];

    /**
     * New Attributes
     *
     * @var array
     */
    protected $appends = [
        'custom_fields',
        'has_media',
        'rate'

    ];

    /**
     * @var array
     */
    private static $delivery_price_types = [
        'fixed' => 'fixed',
        'distance' => 'distance',
        'flexible' => 'flexible',
    ];



    /**
     * Allowed attributes to skip appends attributes 
     * This method useful to skip load extra data , also skip load relations when will not be useful
     */
    public function getAllowedAttributesToSkipAppends()
    {
        return ['id', 'name'];
    }
    /**
     * @param Media|null $media
     * @throws \Spatie\Image\Exceptions\InvalidManipulation
     */
    public function registerMediaConversions(Media $media = null)
    {
        $this->addMediaConversion('thumb')
            ->fit(Manipulations::FIT_CROP, 200, 200)
            ->sharpen(10);

        $this->addMediaConversion('icon')
            ->fit(Manipulations::FIT_CROP, 100, 100)
            ->sharpen(10);
    }

    public function customFieldsValues()
    {
        return $this->morphMany('App\Models\CustomFieldValue', 'customizable');
    }

    /**
     * to generate media url in case of fallback will
     * return the file type icon
     * @param string $conversion
     * @return string url
     */
    public function getFirstMediaUrl($collectionName = 'default', $conversion = '')
    {
        $url = $this->getFirstMediaUrlTrait($collectionName);
        $array = explode('.', $url);
        $extension = strtolower(end($array));
        if (in_array($extension, config('medialibrary.extensions_has_thumb'))) {
            return asset($this->getFirstMediaUrlTrait($collectionName, $conversion));
        } else {
            return asset(config('medialibrary.icons_folder') . '/' . $extension . '.png');
        }
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
     * Add Media to api results
     * @return bool
     */
    public function getHasMediaAttribute()
    {
        return $this->hasMedia('image') ? true : false;
    }

    /**
     * Add Media to api results
     * @return bool
     */
    public function getRateAttribute()
    {
        return $this->restaurantReviews()->select(DB::raw('round(AVG(restaurant_reviews.rate),1) as rate'))->first('rate')->rate;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function foods()
    {
        return $this->hasMany(\App\Models\Food::class, 'restaurant_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function galleries()
    {
        return $this->hasMany(\App\Models\Gallery::class, 'restaurant_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function restaurantReviews()
    {
        return $this->hasMany(\App\Models\RestaurantReview::class, 'restaurant_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     **/
    public function users()
    {
        return $this->belongsToMany(\App\Models\User::class, 'user_restaurants')->withPivot('enable_notifications');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     **/
    public function drivers()
    {
        return $this->belongsToMany(\App\Models\User::class, 'driver_restaurants');
    }

    /**
     * Get Types of delivery prices 
     * @return array
     */
    public static function getDeliveryPriceTypes(): array
    {
        return static::$delivery_price_types;
    }

    public function cuisines()
    {
        return $this->belongsToMany(\App\Models\Cuisine::class, 'restaurant_cuisines');
    }
    public function distancesPrices()
    {
        return $this->hasMany(RestaurantDistancePrice::class);
    }
    public function discountables()
    {
        return $this->morphMany('App\Models\Discountable', 'discountable');
    }

    /**
     * Get users who wants to receive notifications
     */
    public function getUsersWhoEnabledNotifications()
    {
        if (!$this->relationLoaded('users')) {
            $this->load('users');
        }
        return $this->users->where('pivot.enable_notifications', 1);
    }
}
