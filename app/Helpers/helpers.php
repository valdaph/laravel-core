<?php

use Illuminate\Support\Facades\Storage;

if (! function_exists('storage_url')) {
    /**
     * Generate the URL to a file in storage.
     *
     * @param  string  $path
     * @param  string  $domain
     * @return string
     */
    function storage_url($path, $domain = null)
    {
        $domain = $domain ?: config('constants.URL.WEB', config('app.url'));

        switch (config('filesystems.default')) {
            case 'public':
                return $domain . Storage::url($path);

            case 's3':
                return Storage::url($path);
            
            default:
                return $path;
        }
    }
}
