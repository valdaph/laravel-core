<?php

namespace Valda\Traits;

trait SilencesModelEvents
{
    /**
     * Execute callback without triggering any events.
     *
     * @param  callable  $callback
     * @return void
     */
    public function silent($callback)
    {
        $dispatcher = $this->getEventDispatcher();

        $this->unsetEventDispatcher();

        try {
            $callback($this);
        } finally {
            $this->setEventDispatcher($dispatcher);
        }
    }

    /**
     * Execute callback without triggering any events if the given value is truthy.
     *
     * @param  mixed  $value
     * @param  callable  $callback
     * @return void
     */
    public function silentWhen($value, $callback)
    {
        $value ? $this->silent($callback) : $callback($this);
    }

    /**
     * Silently update the model in the database.
     *
     * @param  array  $attributes
     * @param  array  $options
     * @return bool
     */
    public function silentUpdate(array $attributes = [], array $options = [])
    {
        $dispatcher = $this->getEventDispatcher();

        $this->unsetEventDispatcher();

        try {
            return $this->update($attributes, $options);
        } finally {
            $this->setEventDispatcher($dispatcher);
        }
    }

    /**
     * Silently update the model in the database when given value is truthy.
     *
     * @param  mixed  $value
     * @param  array  $attributes
     * @param  array  $options
     * @return bool
     */
    public function silentUpdateWhen($value, array $attributes = [], array $options = [])
    {
        return $value
            ? $this->silentUpdate($attributes, $options)
            : $this->update($attributes, $options);
    }
}
