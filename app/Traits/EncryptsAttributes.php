<?php

namespace Valda\Traits;

trait EncryptsAttributes
{
    /**
     * The attributes that should be encrypted.
     *
     * @var array
     */
    protected $encrypts = [];

    /**
     * Get an attribute from the model.
     *
     * @param  string  $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);

        if (in_array($key, $this->encrypts)) {
            $value = decrypt($value);
        }

        return $value;
    }

    /**
     * Set a given attribute on the model.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return $this
     */
    public function setAttribute($key, $value)
    {
        if (in_array($key, $this->encrypts)) {
            $value = encrypt($value);
        }

        return parent::setAttribute($key, $value);
    }

    /**
     * Convert the model's attributes to an array.
     *
     * @return array
     */
    public function attributesToArray()
    {
        $attributes = parent::attributesToArray();

        foreach ($attributes as $key => $value) {
            if (in_array($key, $this->encrypts) && !empty($value)) {
                $attributes[$key] = decrypt($value);
            }
        }

        return $attributes;
    }
}
