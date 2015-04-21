<?php
/**
 * Stop Class.
 *
 * @author Chen Ling <chling.sbu@gmail.com>
 * @copyright Chen Ling 2015
 * Released under the MIT License
 *
 */

class Stop {
	private $id;
	private $lat;
	private $long;
	private $agency;
	private $title;


  public function __construct($id, $lat, $long, $agency, $title) {
  	$this->id = $id;
  	$this->lat = $lat;
  	$this->long = $long;
  	$this->agency = $agency;
  	$this->title = $title;
  }

  /**
   * Returns the data on the object as an array.
   */
  public function to_array() {
  	return [
  	  'id'         => $this->id,
  	  'lat'        => $this->lat,
  	  'long'       => $this->long,
  	  'agency'     => $this->agency,
  	  'title'      => $this->title
  	];
  }


	public function set_id($id) {
		$this->id = $id;
	}

	public function set_lat($lat) {
		$this->lat = $lat;
	}

	public function set_lon($long) {
		$this->long = $long;
	}

	public function set_agency($agency) {
		$this->agency = $agency;
	}

	public function set_title($title) {
		$this->title = $title;
	}

	public function get_id() {
		return $this->id;
	}

	public function get_lat() {
		return $this->lat;
	}

	public function get_long() {
		return $this->long;
	}

	public function get_agency() {
		return $this->agency;
	}

	public function get_title() {
		return $this->title;
	}
}