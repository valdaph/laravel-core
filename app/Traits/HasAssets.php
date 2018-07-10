<?php

namespace Valda\Traits;

use Illuminate\Support\Facades\Storage;

trait HasAssets
{
    /**
     * Boot the trait.
     *
     * @return void
     */
    public static function bootHasAssets()
    {
        static::updating(function ($model) {
            $model->deleteOldAssets();
        });

        static::deleting(function ($model) {
            $model->deleteOldAssets(true);
        });
    }

    /**
     * Delete old assets.
     *
     * @param  bool  $force
     * @return void
     */
    public function deleteOldAssets($force = false)
    {
        $assets = $this->getAssets();

        foreach ($assets as $asset) {
            if (!$force && !$this->isDirty($asset)) {
                continue;
            }

            $asset = filter_var($this->getOriginal($asset), FILTER_SANITIZE_URL);

            if (!filter_var($asset, FILTER_VALIDATE_URL) && Storage::exists($asset)) {
                Storage::delete($asset);
            }
        }
    }

    /**
     * Get the assets of the model.
     *
     * @return array
     */
    public function getAssets()
    {
        return $this->assets ?: [];
    }
}