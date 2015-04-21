<?php
/**
 * Simple PHP class to get distance between two coordinates
 *
 * Code transformed from Derick Rethans's example code
 * Please refer to his website for Licensing Questoins
 * http://derickrethans.nl/spatial-indexes-calculating-distance.html
 */


class SpatialDistance {

  public function get_distance($lat_a, $lon_a, $lat_b, $lon_b) {

  	// convert from degrees to radians
    $lat_a = deg2rad($lat_a); $lon_a = deg2rad($lon_a);
    $lat_b = deg2rad($lat_b); $lon_b = deg2rad($lon_b);

    // calculate absolute difference for latitude and longitude
    $d_lat = ($lat_a - $lat_b);
    $d_lon = ($lon_a - $lon_b);

    // do trigonometry magic
    $d = sin($d_lat/2) * sin($d_lat/2) + cos($lat_a) * cos($lat_b) * sin($d_lon/2) *sin($d_lon/2);
    $d = 2 * asin(sqrt($d));
    return $d * 6371;
  }
}