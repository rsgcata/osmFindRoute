<?php

error_reporting(E_ALL);

require 'vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use GuzzleHttp\Client;

define('API_KEY', 'YOUR_API_KEY_GOES_HERE');
define('MAX_NUM_OF_COORDS', 30);

$request = Request::createFromGlobals();
$response = new Response();

if (strpos($request->headers->get('Content-Type'), 'application/json') === 0) {
    $data = json_decode($request->getContent(), true);
    $request->request->replace(is_array($data) ? $data : array());
}

/**
 * Check if coordinates are valid
 * 
 * @param array $coordinates
 * @return boolean
 */
function areCoordinatesValid($coordinates)
{
    $coordsValid = true;

    if (is_array($coordinates)
            && !empty($coordinates)
            && count($coordinates) <= MAX_NUM_OF_COORDS) {
        $coordValidationRes = array_reduce($coordinates, function($carry, $item) {
            if ((is_float($item[0]) || is_int($item[0])) 
                    && (is_float($item[1]) || is_int($item[1])) 
                    && $item[0] <= 180 
                    && $item[0] >= -180
                    && $item[1] <= 90 
                    && $item[1] >= -90) {
                return $carry && true;
            }
            else {
                return false;
            }
        }, true);

        if (!$coordValidationRes) {
            $coordsValid = false;
        }
    }
    else {
        $coordsValid = false;
    }

    return $coordsValid;
}

/**
 * Check if the profile is valid
 * 
 * @param string $profile
 * @return boolean
 */
function isProfileValid($profile)
{
    if (!in_array($profile, ['driving-car', 'foot-walking'])) {
        return false;
    }
    else {
        return true;
    }
}

if ($request->getRequestUri() === '/multipoint') {
    $client = new Client([
        // Base URI is used with relative requests
        'base_uri' => 'https://api.openrouteservice.org',
        // You can set any number of default request options.
        'timeout' => 10.0,
    ]);

    $coordinates = $request->request->get('coordinates', null);
    $profile = $request->request->get('profile', null);

    // Validate input
    if (!areCoordinatesValid($coordinates) || !isProfileValid($profile)) {
        error_log('Invalid data sent.' . var_export($coordinates, true) 
                . ' ' . var_export($profile, true));
        $response->setStatusCode(400);
        $response->send();
        exit();
    }

    $radiuses = [];
    
    foreach($coordinates as $val) {
        $radiuses[] = -1;
    }
    
    $apiResponse = $client->request('POST', '/v2/directions/' . $profile . '/geojson', [
        'headers' => [
            'Authorization' => API_KEY
        ],
        'json' => [
            'coordinates' => $coordinates,
            'radiuses' => $radiuses
        ],
        'http_errors' => false
    ]);

    if ($apiResponse->getStatusCode() !== 200) {
        error_log('Open Route Service API call failed with error : "' 
                . $apiResponse->getStatusCode() . ' ' . $apiResponse->getReasonPhrase() . '.'
                . ' ' . $apiResponse->getBody() . '".');

        $response->setStatusCode(500);
    }
    else {
        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'application/json; ' . 'charset=UTF-8');
        $response->setContent($apiResponse->getBody());
    }

    $response->send();
    exit();
}
else {
    return false;
}