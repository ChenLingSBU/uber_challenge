<?php
/**
 * Ajax Handler Script.
 * This script is a bridge between backend and frontend to communicate with each other.
 *
 *
 * @author Chen Ling <chling.sbu@gmail.com>
 * @copyright Chen Ling 2015
 * Released under the MIT License
 *
 */



require_once './data_querier_class.php';

// this block of code deal with ajax request
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
  $command = $_GET['command'];

  // init geohash. To improve web performance we attempt to get geohash from cache file,
  // if fail, we will re-calculate geohash.
  $geohash = init_geohash();

  if ($command == 'get_nearby_stops') {
    $lat = $_GET['lat'];
    $long = $_GET['lng'];
    $return_array = [];
    $data_querier = new DataQuerier();
    $nearby_stops = $data_querier->get_nearby_stops($lat, $long, $geohash);
    foreach($nearby_stops as $stop) {
    $return_array[] = $stop->to_array();
    }
    echo json_encode($return_array);
    die();
  }

  if ($command == 'get_departures') {
    $stop_id = $_GET['stopId'];
    $agency = $_GET['agency'];
    $return_array = [];
    $data_querier = new DataQuerier();
    $departures = $data_querier->get_departures_for_stop($stop_id, $agency);
    foreach($departures as $departure) {
      $return_array[] = $departure->to_array();
    }
    echo json_encode($return_array);
    die();
  }
}

// if no cache found, we will re-calculate geo hash and cache them.
// in the re-calculate part, we will call init stops to get all the bus stops.
function init_geohash() {
  $cached_geohash = '../../data/geohash.txt';
  $geohash = [];
  if (($file_handler = file_get_contents($cached_geohash)) !== false) {
    $geohash = unserialize($file_handler);
  } else {
    $data_supplier = new DataSupplier();
    $stops = init_stops();
    $geohash = $data_supplier->get_geo_hash($stops);
    file_put_contents($cached_geohash, serialize($geohash));
  }
  return $geohash;
}

// we will attempt to retrieve stops in cach file,
// if failed we re-get all the stops through Next Bus RESTful API Call and cache them.
function init_stops() {
  $cached_stops = '../../data/stops.txt';
  $stops = [];
  if (($file_handler = file_get_contents($cached_stops)) !== false) {
    $stops = unserialize($file_handler);
  } else {
    $data_supplier = new DataSupplier();
    $stops = $data_supplier->get_all_stops();
    file_put_contents($cached_stops, serialize($stops));
  }
  return $stops;
}
