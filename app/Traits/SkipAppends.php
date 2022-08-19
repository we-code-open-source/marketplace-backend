<?php

namespace App\Traits;

/**
 * Skip appends it is traits for \App\Models to skip load appends automatically
 */
trait SkipAppends
{

    /**
     * Allowed attributes to skip appends attributes 
     * This method useful to skip load extra data , also skip load relations when will not be useful
     */
    public function getAllowedAttributesToSkipAppends()
    {
        return ['id', 'name'];
    }


    /**
     * Convert the model instance to an array.
     *
     * @return array
     */
    public function toArray()
    {
        if ($this->checkIfCanSkipAppends()) {
            $this->setAppends([]);
        }

        return parent::toArray();
    }

    /**
     * This method to check if can skip appends or nt
     * it checks lenght and all loaded attributes must be exists in getAllowedAttributesToSkipAppends()
     * 
     * @return boolean
     */
    protected function checkIfCanSkipAppends()
    {
        $keys = array_keys($this->attributes);
        $allowed_attributes = $this->getAllowedAttributesToSkipAppends();
        if (count($allowed_attributes) > count($keys)) {
            return false;
        }
        foreach ($keys as $k) {
            if (!in_array($k, $allowed_attributes)) {
                return false;
            }
        }
        return true;
    }
}
