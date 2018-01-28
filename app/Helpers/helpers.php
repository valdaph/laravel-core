<?php

use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager as Image;

if (!function_exists('is_image')) {
    /**
     * Check if the given URL or file is a valid image.
     *
     * @param  mixed  $image
     * @return boolean
     */
    function is_image($image)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_NOBODY, 1);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        if (curl_exec($ch) !== false) {
            $type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

            curl_close($ch);

            try {
                return strpos($type, 'image/') === 0 && Image::make($url);
            } catch (\Exception $e) {
                return false;
            }
        }

        curl_close($ch);

        return false;
    }
}

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
