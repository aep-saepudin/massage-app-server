<?php
/**
 * Calculates the distance between two points, given their 
 * latitude and longitude, and returns an array of values 
 * of the most common distance units
 *
 * @param  {coord} $lat1 Latitude of the first point
 * @param  {coord} $lon1 Longitude of the first point
 * @param  {coord} $lat2 Latitude of the second point
 * @param  {coord} $lon2 Longitude of the second point
 * @return {array}       Array of values in many distance units
 */
function getDistanceBetweenPoints($lat1, $lon1, $lat2, $lon2) {
    $theta = $lon1 - $lon2;
    $miles = (sin(deg2rad($lat1)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)));
    $miles = acos($miles);
    $miles = rad2deg($miles);
    $miles = $miles * 60 * 1.1515;
    $feet = $miles * 5280;
    $yards = $feet / 3;
    $kilometers = $miles * 1.609344;
    $meters = $kilometers * 1000;
    return compact('miles','feet','yards','kilometers','meters'); 
}