<?php

namespace Valda\Traits;

trait SilencesModelEvents
{
    /**
     * Execute callback's changes without triggering any events.
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
     * Execute callback's changes without triggering any events if the given "value" is true.
     *
     * @param  mixed  $value
     * @param  callable  $callback
     * @return void
     */
    public function silentWhen($value, $callback)
    {
        $value ? $this->silent($callback) : $callback($this);
    }
}
