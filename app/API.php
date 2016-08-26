<?php
/**
 * Author: MurDaD
 * Author URL: https://github.com/MurDaD
 *
 * Description: API class
 */

namespace app;


class API extends APIHelper
{

    /**
     * API constructor.
     * @param $url
     * @param $server_token
     * @param $client_id
     * @param $client_secret
     */
    public function __construct($url, $server_token, $client_id, $client_secret)
    {
        parent::__construct($url, $server_token, $client_id, $client_secret);
    }

    /**
     * Authenticate user.
     * Saves access key to local var $access_key
     *
     * TODO: make it
     */
    public function auth()
    {

    }

    /**
     * Request estimate for a ride
     *
     * @param $start_latitude
     * @param $start_longitude
     * @param $end_latitude
     * @param $end_longitude
     * @return string
     */
    public function getEstimates($start_latitude, $start_longitude, $end_latitude, $end_longitude)
    {
        $this->construct_url('/estimates/price', 1, true);
        $request = [
            'start_latitude' => $start_latitude,
            'start_longitude' => $start_longitude,
            'end_latitude' => $end_latitude,
            'end_longitude' => $end_longitude,
        ];
        if(!$this->auth_key) {
            $request['server_token'] = $this->server_token;
        }
        return $this->request($this->url, 'GET', $request);
    }
}