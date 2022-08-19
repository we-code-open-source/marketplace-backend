<?php

namespace App\Collections;

use Illuminate\Database\Eloquent\Collection;

class FoodCollection extends Collection
{

    public function loadExtraGroupsIfExists()
    {
        $this->filter->loadExtraGroupsIfExists();
        return $this;
    }
}
