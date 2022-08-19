<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VerficationCode extends Model
{

    public $fillable = [
        'code',
        'token',
        'user_id',
        'phone',
        'created_at',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}
