<?php

namespace Valda\Traits;

trait MasksAttributes
{
    /**
     * The attributes that should be masked.
     *
     * @var array
     */
    protected $masks = [];

    /**
     * Check if the given attribute is maskable.
     *
     * @param  string  $attribute
     * @return bool
     */
    public function isMaskable($attribute)
    {
        return array_key_exists($attribute, $this->masks)
            || is_numeric(array_search($attribute, $this->masks));
    }

    /**
     * Get the attribute's mask options.
     *
     * @param  string  $attribute
     * @return array
     */
    public function getMaskOptions($attribute)
    {
        if (array_key_exists($attribute, $this->masks)) {
            return $this->masks[$attribute];
        }

        return [];
    }

    /**
     * Mask a value by the given options.
     *
     * @param  string  $key
     * @param  string|array  $options
     * @return mixed
     */
    public function maskValue($value, $options = [])
    {
        $mask = array_key_exists('mask', $options) ? $options['mask'] : 'X';
        $length = strlen($value);

        unset($options['mask']);

        if (count($options) === 0) {
            return str_repeat($mask, $length);
        }

        if (array_key_exists('left', $options)) {
            $value = str_repeat($mask, $options['left']) . mb_substr($value, $options['left']);
        }

        if (array_key_exists('right', $options)) {
            $value = mb_substr($value, 0, $options['right'] * -1) . str_repeat($mask, $options['right']);
        }

        if (array_key_exists('show_left', $options)) {
            $value = mb_substr($value, 0, $options['show_left']) . str_repeat($mask, $length - $options['show_left']);
        }

        if (array_key_exists('show_right', $options)) {
            $value = str_repeat($mask, $length - $options['show_right']) . mb_substr($value, $options['show_right'] * -1);
        }

        return $value;
    }

    /**
     * Make the given, typically masked, attributes unmasked.
     *
     * @param  array|string  $attributes
     * @return $this
     */
    public function unmask($attributes)
    {
        $masks = $this->masks;
        $attributes = (array) $attributes;

        $masks = array_diff_key($masks, array_flip($attributes));
        $masks = array_diff($masks, $attributes);

        $this->masks = $masks;

        return $this;
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

        if ($this->isMaskable($key)) {
            $value = $this->maskValue($value);
        }

        return $value;
    }

    /**
     * Convert the model's attributes to an array.
     *
     * @return array
     */
    public function attributesToArray()
    {
        $attributes = parent::attributesToArray();

        foreach ($attributes as $attribute => $value) {
            if ($this->isMaskable($attribute)) {
                $attributes[$attribute] = $this->maskValue($value, $this->getMaskOptions($attribute));
            }
        }

        return $attributes;
    }
}
