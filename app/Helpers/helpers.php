<?php

use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager as Image;

if (!function_exists('array_not_unique')) {
    /**
     * Returns all duplicates in an array.
     *
     * @param  array  $array
     * @return array
     */
    function array_not_unique($array)
    {
        $dupes = array();

        natcasesort($array);
        reset($array);

        $oldKey = null;
        $oldValue = null;

        foreach ($array as $key => $value) {
            if ($value === null) {
                continue;
            }

            if (strcasecmp($oldValue, $value) === 0) {
                $dupes[$oldKey] = $oldValue;
                $dupes[$key] = $value;
            }

            $oldValue = $value;
            $oldKey = $key;
        }

        return $dupes;
    }
}

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
