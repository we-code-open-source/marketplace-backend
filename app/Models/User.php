<?php

/**
 * File name: User.php
 * Last modified: 2020.06.11 at 16:10:52
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2020
 */

namespace App\Models;

use App\Models\DeviceToken;
use App\Traits\SkipAppends;
use Laravel\Cashier\Billable;
use Spatie\Image\Manipulations;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Class User
 * @package App\Models
 * @version July 10, 2018, 11:44 am UTC
 *
 * @property \App\Models\Cart[] cart
 * @property string name
 * @property string phone_number
 * @property string email
 * @property boolean active
 * @property string password
 * @property string api_token
 * @property string device_token
 */
class User extends Authenticatable implements HasMedia
{
    use Notifiable;
    use Billable;
    use HasMediaTrait {
        getFirstMediaUrl as protected getFirstMediaUrlTrait;
    }
    use HasRoles;
    use SkipAppends;


    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required|string|max:255',
        'phone_number' => 'required|string|max:20|unique:users,phone_number',
        'email' => 'required|string|unique:users,email',
        'active' => 'required|boolean',
        'password' => 'required|min:6|max:32',
    ];
    public $table = 'users';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $fillable = [
        'name',
        'phone_number',
        'email',
        'active',
        'password',
        'api_token',
        'device_token',
        'activated_at',
    ];
    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'name' => 'string',
        'phone_number' => 'string',
        'email' => 'string',
        'active' => 'boolean',
        'password' => 'string',
        'api_token' => 'string',
        'device_token' => 'string',
        'remember_token' => 'string',
        'activated_at' => 'datetime',
    ];
    /**
     * New Attributes
     *
     * @var array
     */
    protected $appends = [
        'custom_fields',
        'has_media',
        'enable_notifications',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
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
     * Specifies the user's FCM token
     *
     * @return string
     */
    public function routeNotificationForFcm($notification)
    {
        return $this->getDeviceTokens();
    }

    /**
     * Specifies the user's Sms phone
     *
     * @return string
     */
    public function routeNotificationForSms($notification)
    {
        return $this->phone_number;
    }

    /**
     * Specifies the user's Whatsapp phone
     *
     * @return string
     */
    public function routeNotificationForWhatsapp($notification)
    {
        return $this->phone_number;
    }



    /**
     * @param Media|null $media
     * @throws \Spatie\Image\Exceptions\InvalidManipulation
     */
    public function registerMediaConversions(\Spatie\MediaLibrary\Models\Media $media = null)
    {
        $this->addMediaConversion('thumb')
            ->fit(Manipulations::FIT_CROP, 200, 200)
            ->sharpen(10);

        $this->addMediaConversion('icon')
            ->fit(Manipulations::FIT_CROP, 100, 100)
            ->sharpen(10);
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
        if ($url) {
            $array = explode('.', $url);
            $extension = strtolower(end($array));
            if (in_array($extension, config('medialibrary.extensions_has_thumb'))) {
                return asset($this->getFirstMediaUrlTrait($collectionName, $conversion));
            } else {
                return asset(config('medialibrary.icons_folder') . '/' . $extension . '.png');
            }
        } else {
            return asset('images/avatar_default.png');
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
            //            ->where('custom_fields.in_table', '=', true)
            ->select(['value', 'view', 'name'])
            ->get()->toArray();

        return convertToAssoc($array, 'name');
    }

    public function customFieldsValues()
    {
        return $this->morphMany('App\Models\CustomFieldValue', 'customizable');
    }

    /**
     * Add Media to api results
     * @return bool
     */
    public function getHasMediaAttribute()
    {
        return $this->hasMedia('avatar') ? true : false;
    }

    /**
     * Add enable_notifications to api results
     * @return bool
     */
    public function getEnableNotificationsAttribute()
    {
        if ($this->relationLoaded('restaurants')) {
            return $this->restaurants->first()->pivot->enable_notifications;
        }

        return null;
    }

    /**
     * Set device token for user 
     * check if token exists and linked to another user , update it to be linked to current user 
     
     * @param $token 
     * @return DeviceToken instance
     */
    public function setDeviceToken($token = null)
    {
        if (!$token) {
            $token =  request()->input('device_token');
        }
        return  DeviceToken::updateOrCreate(
            ['token' => $token],
            ['token' => $token, 'user_id' => $this->id]
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     **/
    public function restaurants()
    {
        return $this->belongsToMany(\App\Models\Restaurant::class, 'user_restaurants')->withPivot('enable_notifications');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function cart()
    {
        return $this->hasMany(\App\Models\Cart::class, 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     **/
    public function driver()
    {
        return $this->hasOne(\App\Models\Driver::class, 'user_id');
    }

    /**
     * Get the user's address.
     */
    public function address()
    {
        return $this->morphOne(\App\Models\DeliveryAddress::class, 'user');
    }
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     **/
    public function verfication_code()
    {
        return $this->hasOne(\App\Models\VerficationCode::class, 'user_id');
    }
    /**
     * Get all of the Device_Tokens for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function deviceTokens()
    {
        return $this->hasMany(DeviceToken::class);;
    }

    public function getDeviceTokens()
    {
        return $this->deviceTokens()->pluck('token')->toArray();
    }
}
