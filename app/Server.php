<?php
/**
 * Author: MurDaD
 * Author URL: https://github.com/MurDaD
 *
 * Description: API class
 */

namespace app;

class Server extends API
{
    private $service_name_before = 'After';         // Service name add before Uber service name
    private $service_name_after = '';               // Service name add after Uber service name
    private $discount = '20';                       // Discount in %
    private $result = [];                           // Result encoded to json and returned

    /**
     * Server constructor.
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
     * Request estimate for a ride
     *
     * @param $start_latitude
     * @param $start_longitude
     * @param $end_latitude
     * @param $end_longitude
     * @return string
     */
    public function estimate($start_latitude, $start_longitude, $end_latitude, $end_longitude)
    {
        $estimates = parent::getEstimates($start_latitude, $start_longitude, $end_latitude, $end_longitude);
        $return = [];
        foreach($estimates->prices as $e) {
            if(is_numeric($e->high_estimate)) {
                if($e->high_estimate != $e->low_estimate) {
                    $estimate = $this->calcDiscount($e->low_estimate);
                    $estimate.= '-';
                    $estimate.= $this->calcDiscount($e->high_estimate);
                } else {
                    $estimate = $this->calcDiscount($e->high_estimate);
                }
                $estimate = '$'.$estimate;
            } else {
                $estimate = $e->estimate;
            }

            $arr = [
                'estimate'  => $estimate,
                'name'      => $this->service_name_before.$e->localized_display_name.$this->service_name_after,

            ];
            array_push($return, $arr);
        }
        $this->result = ['result' => $return];
        echo $this->returnResult();
    }

    /**
     * Gets coordinates from Google API address
     * $type is used to display error (address from or to)
     *
     * @param string $address
     * @param $type
     */
    public function getCoordsFromAddress($address = '', $type = '')
    {
        if($type) $type .= ' ';
        if($address) {
            $url = $this->construct_url('/maps/api/geocode/json', '', false, 'maps.google.com', true);
            $fields = [
                'address' => $address
            ];
            $res = $this->request($url, 'GET', $fields);
            if(count($res->results) > 0) {
                if($res->results[0]) {
                    $loc = $res->results[0]->geometry->location;
                } else {
                    $loca = $res->results->geometry->location;
                }
                $this->result = [
                    'lat' => $loc->lat,
                    'lng' => $loc->lng
                ];
            } else {
                die($this->returnError('Address '.$type.'not found'));
            }
        } else {
            die($this->returnError('Address '.$type.'is empty'));
        }
        echo $this->returnResult();
    }

    /**
     * Calculates discount for given price
     *
     * @param $price
     * @return string
     */
    private function calcDiscount($price)
    {
        return number_format($price / 100 * (100 - $this->discount), 2);
    }

    /**
     * Return local API error
     *
     * @param $message
     */
    private function returnError($message)
    {
        $this->result = [
            'result' => 'error',
            'message' => $message
        ];
        echo $this->returnResult();
    }

    /**
     * Converts array to JSON
     *
     * @return string
     */
    public function returnResult()
    {
        return json_encode($this->result);
    }
}