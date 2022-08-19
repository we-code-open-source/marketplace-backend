<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class UnregisteredCustomer extends Model
{

    public $table = 'unregistered_customers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $fillable = [
        'name',
        'phone',
        'mobile',
        'restaurant_id',
    ];



    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     **/
    public function restaurant()
    {
        return $this->belongsTo(\App\Models\Restaurant::class);
    }


    /**
     * Get the user's address.
     */
    public function address()
    {
        return $this->morphOne(\App\Models\DeliveryAddress::class, 'user');
    }
}
