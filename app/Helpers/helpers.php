<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

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

if (!function_exists('date_range')) {
    /**
     * Get all the dates between two dates.
     *
     * @param  string  $start
     * @param  string  $end
     * @param  string  $format
     * @return array
     */
    function date_range($start, $end, $format = 'Y-m-d')
    {
        $dates = [];
        $interval = new DateInterval('P1D');

        $realEnd = new DateTime($end);
        $realEnd->add($interval);

        $period = new DatePeriod(new DateTime($start), $interval, $realEnd);

        foreach ($period as $date) {
            $dates[] = $date->format($format);
        }

        return $dates;
    }
}

if (!function_exists('is_date')) {
    /**
     * Check if given string is a valid date.
     *
     * @param  string  $data
     * @param  string  $format
     * @return boolean
     */
    function is_date($date, $format = 'Y-m-d')
    {
        try {
            $d = Carbon::createFromFormat($format, $date);

            return $d && $d->format($format) === $date;
        } catch (\Exception $e) {
            return false;
        }
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

        curl_setopt($ch, CURLOPT_URL, $image);
        curl_setopt($ch, CURLOPT_NOBODY, 1);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        if (curl_exec($ch) !== false) {
            $type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

            curl_close($ch);

            try {
                return strpos($type, 'image/') === 0 && Image::make($image);
            } catch (\Exception $e) {
                return false;
            }
        }

        curl_close($ch);

        return false;
    }
}

if (!function_exists('is_social_tag')) {
    /**
     * Check if the given string is a valid social media tag.
     *
     * @param  string  $tag
     * @param  string|null  $tagName
     * @return boolean
     */
    function is_social_tag($tag, &$tagName = null)
    {
        $tagRegex = '/^(@|\/)?([A-Za-z0-9_]+)$/';
        $isTag = preg_match($tagRegex, $tag, $tagMatches);

        if ($isTag) {
            $tagName = $tagMatches[2];

            return true;
        }

        return false;
    }
}

if (!function_exists('time_to_12')) {
    /**
     * Convert 24 hour time to 12 hour time.
     *
     * @param  string  $time
     * @return string
     */
    function time_to_12($time)
    {
        return date('h:i A', strtotime($time));
    }
}

if (!function_exists('time_to_24')) {
    /**
     * Convert 12 hour time to 24 hour time.
     *
     * @param  string  $time
     * @return string
     */
    function time_to_24($time)
    {
        return date('H:i', strtotime($time));
    }
}

if (!function_exists('url_fix')) {
    /**
     * Fix URLs by adding the scheme.
     *
     * @param  string  $time
     * @return string
     */
    function url_fix($url, $secure = false, $www = false)
    {
        $scheme = $secure ? 'https://' : 'http://';
        $subdomain = $www ? 'www.' : '';

        if (preg_match('/^(https?:\/\/)?(www\.)?(.*)/', $url, $matches)) {
            return $scheme . $subdomain . $matches[3];
        }

        return $scheme . $subdomain . $url;
    }
}
