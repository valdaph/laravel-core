<?php

namespace Valda\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;
use Valda\Traits\EncryptsAttributes;
use Valda\Traits\MasksAttributes;
use Valda\Traits\SilencesModelEvents;

class Model extends BaseModel
{
    use EncryptsAttributes, SilencesModelEvents;
    use MasksAttributes {
        getAttribute as getMaskedAttribute;
        attributesToArray as maskedAttributesToArray;
    }

    /**
     * Get an attribute from the model.
     *
     * @param  string  $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);
        $maskedValue = $this->getMaskedAttribute($key);

        return $value !== $maskedValue ? $maskedValue : $value;
    }

    /**
     * Convert the model's attributes to an array.
     *
     * @return array
     */
    public function attributesToArray()
    {
        $attributes = parent::attributesToArray();

        return array_merge($attributes, $this->maskedAttributesToArray());
    }
}
