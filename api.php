<?php
/**
 * Author: MurDaD
 * Author URL: https://github.com/MurDaD
 *
 * Description: Ajax local API
 * Just a regular GET request in case only one functionality is required
 */
include 'config.php';

switch($_GET['request']) {
    case 'getAddrCoords':
        $api = initAPI();
        $address = addslashes(htmlspecialchars($_GET['location']));
        echo $api->getCoordsFromAddress($address);
        unset($api);
        break;
    case 'getEstimate':
        $api = initAPI();
        $start_latitude = addslashes(htmlspecialchars($_GET['start_latitude']));
        $start_longitude = addslashes(htmlspecialchars($_GET['start_longitude']));
        $end_latitude = addslashes(htmlspecialchars($_GET['end_latitude']));
        $end_longitude = addslashes(htmlspecialchars($_GET['end_longitude']));
        echo $api->estimate($start_latitude, $start_longitude, $end_latitude, $end_longitude);
        unset($api);
        break;
}

function initAPI() {
    return new \app\Server(
        \includes\Settings::get('api_url'),
        \includes\Settings::get('api_token'),
        \includes\Settings::get('api_client_id'),
        \includes\Settings::get('api_client_secret')
    );
}
