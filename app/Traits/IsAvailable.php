<?php

namespace App\Traits;

trait IsAvailable {

    public function scopeAvailable($query)
    {
        return $query->where('is_available',1);
    }

    public function scopeUnavailable($query)
    {
        return $query->where('is_available',0);
    }
}