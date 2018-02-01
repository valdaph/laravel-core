<?php

namespace Valda\Libraries;

use GuzzleHttp\Client;

class Geocode
{
    /**
     * The Google Maps API key.
     *
     * @var string
     */
    protected $key;

    /**
     * The Guzzle HTTP client.
     *
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * Create a new Google Maps instance.
     *
     * @param  string|null  $key
     * @param  string|null  $baseUrl
     * @return void
     */
    public function __construct($key = null, $baseUrl = null)
    {
        $this->key = $key ?: config('services.google_maps.key');
        $this->client = new Client(['base_uri' => $baseUrl ?: config('services.google_maps.geocoding_url')]);
    }

    /**
     * Request location info from the given address.
     *
     * @param  string  $address
     * @param  string  $outputFormat
     * @return void
     */
    protected function geocode($address)
    {
        $response = $this->client->get('', ['query' => [
            'address' => $address,
            'key' => $this->key,
        ]]);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Request location info from the given coordinates.
     *
     * @param  string  $latitude
     * @param  string  $longitude
     * @param  string  $outputFormat
     * @return void
     */
    protected function reverseGeocode($latitude, $longitude)
    {
        $response = $this->client->get('', ['query' => [
            'latlng' => $latitude . ',' . $longitude,
            'key' => $this->key,
        ]]);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Dynamically handle calls into the Google Maps instance.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     *
     * @throws \BadMethodCallException
     */
    public static function __callStatic($method, $parameters)
    {
        if (is_callable([__CLASS__, $method])) {
            return call_user_func_array([new self, $method], $parameters);
        }

        throw new \BadMethodCallException();
    }
}
