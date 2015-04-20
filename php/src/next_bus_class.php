<?php
/**
 * Next Bus Class
 * It's an encapsulation class for all the core methods to retrieve data from Next Bus RESTful API.
 *  
 * @author Chen Ling <chling.sbu@gmail.com>
 * @copyright Chen Ling 2015
 * Released under the MIT License
 *
 */

require_once './stop_class.php';
require_once './departure_class.php';

// set running time limit for this script to 600s, 
// We don't have the cached files when we run this app for the first time. 
// this script will use Next Bus RESTful API to get all the bus stops for sf-muni which is a little bit slow.
set_time_limit(600);
class NextBus {

  const BASE_URL = 'http://webservices.nextbus.com/service/publicXMLFeed?';
  const AGENCY = 'sf-muni';

	public function get_routes_for_agency($agency = self::AGENCY) {
		$query = [
		  'command' => 'routeList',
		  'a'       => $agency
		];
		$url = self::BASE_URL . http_build_query($query);
		$xml_obj = simplexml_load_string(file_get_contents($url));
		return $this->parse_routes($xml_obj);
	}


	public function get_stops_for_route($route, $agency = self::AGENCY) {
		$query = [
		  'command' => 'routeConfig',
		  'a'       => $agency,
		  'r'       => $route
		];
		$url = self::BASE_URL . http_build_query($query);
		$xml_obj = simplexml_load_string(file_get_contents($url));
		return $this->parse_stops($xml_obj, $agency);
	}

	public function get_departures_for_stop($stop_id, $stop_agency) {
		$query = [
			'command' => 'predictions',
			'a'       => $stop_agency,
			'stopId'  => $stop_id
		];
		$url = self::BASE_URL . http_build_query($query);
		$xml_obj = simplexml_load_string(file_get_contents($url));
    return $this->parse_departures($xml_obj);
	}

	public function parse_routes($xml_obj) {
		$routes = [];
		foreach($xml_obj->children() as $route) {
		  $routes[] = $route['tag']->__toString();
		}
		return $routes;
	}

	public function parse_stops($xml_obj, $agency) {
		$stops = [];
		$stops_obj = $xml_obj->xpath('/body/route/stop');
		foreach ($stops_obj as $stop) {
			$id = $stop['stopId']->__toString();
			$lat = $stop['lat']->__toString();
			$long = $stop['lon']->__toString();
			$title = $stop['title']->__toString();
			$stops[] = new Stop($id, $lat, $long, $agency, $title);
		}
		return $stops;
	}

	public function parse_departures($xml_obj) {
		$departures = [];
		$predictions_obj = $xml_obj->xpath('/body/predictions');
		foreach ($predictions_obj as $prediction) {
			if (!empty($prediction['dirTitleBecauseNoPredictions'])) {
				$departure = new Departure();
				$dir_title = $prediction['dirTitleBecauseNoPredictions']->__toString();
				$route_tag = $prediction['routeTag']->__toString();
			  $stop_title = $prediction['stopTitle']->__toString();
				$departure->set_direction($dir_title);
				$departure->set_no_prediction(true);
				$departure->set_route_tag($route_tag);
				$departures[] = $departure;
				continue;
			}
			$route_tag = $prediction['routeTag']->__toString();
			$stop_title = $prediction['stopTitle']->__toString();
			foreach($prediction->children() as $direction) {
				$dir_title = (string)$direction['title'];
				foreach($direction->children() as $departure) {
					$epoch_time = $departure['epochTime']->__toString();
					$departure = new Departure();
					$departure->set_stop_title($stop_title);
					$departure->set_route_tag($route_tag);
					$departure->set_direction($dir_title);
				  $departure->set_epoch_time($epoch_time);
				  $departures[] = $departure;
				}
			}
		}
		return $departures;
	}
}
