<?php
/**
 * Data Supplier Class
 * This class used to supply data as geohash and all the bus stops
 *
 *
 * @author Chen Ling <chling.sbu@gmail.com>
 * @copyright Chen Ling 2015
 * Released under the MIT License
 *
 */

require_once './next_bus_class.php';
require_once '../lib/geohash_class.php';

set_time_limit(600);
class DataSupplier {

  // GEO_PRECISION is the length of geohash's prefix we want to query for the adjacent bounding box.
  // GEO_PRECISION = 6, the distance is about 610 meters.
	const GEO_PRECISION = 6;

  // calculate and get geohash for all the bus stops.
	public function get_geo_hash($stops) {
		$geohash = new Geohash();
		$hashmap = [];
		foreach($stops as $stop) {
			$hash = $geohash->encode($stop->get_lat(), $stop->get_long());
			$prefix = substr($hash, 0, self::GEO_PRECISION);
			$hashmap[$prefix][] = $stop;
		}
		return $hashmap;
	}

  // get all the bus stops
	public function get_all_stops($agency = NextBus::AGENCY) {
		$next_bus = new NextBus();
	  $routes = $next_bus->get_routes_for_agency($agency);
	  $stops = [];
	  foreach($routes as $route) {
		  $temp_stops = $next_bus->get_stops_for_route($route, $agency);
		  $stops = array_merge($stops, $temp_stops);
	  }
	  $stops = $this->purge_duplicate_stops($stops);
	  return $stops;
	}

  // There might have duplicate stops, delete duplicate ones.
	public function purge_duplicate_stops($stops) {
		$filtered_stops = [];
		$already_taken = [];
		foreach($stops as $stop) {
			$stop_key = $stop->get_agency() . ':' . $stop->get_id();
			if (empty($already_taken[$stop_key])) {
				$filtered_stops[] = $stop;
				$already_taken[$stop_key] = true;
			}
		}
		return $filtered_stops;
	}

}
