<?php
/**
 * Data Querier Class
 * This class provides all the functions for querying data from frontend.
 *
 * @author Chen Ling <chling.sbu@gmail.com>
 * @copyright Chen Ling 2015
 * Released under the MIT License
 *
 */

require_once './data_supplier_class.php';
require_once '../lib/spatial_distance_class.php';

class DataQuerier {

	public function get_departures_for_stop($stop_id, $stop_agency) {
		$next_bus = new NextBus();
		return $next_bus->get_departures_for_stop($stop_id, $stop_agency);
	}

  public function get_nearby_stops($lat, $lon, $stops_geo_hashmap) {
  	$stops = [];
  	$spatial_distance = new SpatialDistance();
  	$neighbors_geo_hash = $this->get_adjacent_neighbors($lat, $lon);
  	$potential_stops = $this->get_potential_stops($stops_geo_hashmap, $neighbors_geo_hash);
  	foreach($potential_stops as $stop) {
  		// only keep stops who are within 0.5 km from the query position.
  		if ($spatial_distance->get_distance($lat, $lon, $stop->get_lat(), $stop->get_long()) <= 0.5) {
  			$stops[] = $stop;
  		}
  	}
  	return $stops;
  }

  public function get_adjacent_neighbors($lat, $lon) {
  	$geo_hash = new Geohash();
  	$hash = $geo_hash->encode($lat, $lon);
  	$prefix = substr($hash, 0, DataSupplier::GEO_PRECISION);
  	$neighbors = $geo_hash->neighbors($prefix);
  	$neighbors[] = $prefix;
  	return $neighbors;
  }

  public function get_potential_stops($stops_geo_hashmap, $neighbors_geo_hash) {
  	$stops = [];
  	foreach($neighbors_geo_hash as $neighbor) {
  		if (!empty($stops_geo_hashmap[$neighbor])) {
  			$stops = array_merge($stops, $stops_geo_hashmap[$neighbor]);
  		}
  	}
  	return $stops;
  }
}
