<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
/**
 * Class DeviceToken
 * @package App\Models
 * @version Feb 4, 2022, 8:07 pm UTC
 *
 * @property \App\Models\User user
 * @property integer user_id
 * @property string delivery_fee
 */
class DeviceToken extends Model
{
    public $table = 'device_tokens';
    public $fillable = ['user_id','token'];

    /**
     * Get the user that owns the DeviceToken
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
