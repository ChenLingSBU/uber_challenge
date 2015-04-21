<?php
/**
 * Departure Class
 *
 * @author Chen Ling <chling.sbu@gmail.com>
 * @copyright Chen Ling 2015
 * Released under the MIT License
 *
 */

class Departure {
  private $stop_title    = null;
  private $epoch_time    = null;
  private $route_tag     = null;
  private $direction     = null;
  private $no_prediction = false;

  public function to_array() {
    return [
      'stop_title'    => $this->stop_title,
      'epoch_time'    => $this->epoch_time,
      'route_tag'     => $this->route_tag,
      'direction'    => $this->direction,
      'no_prediction' => $this->no_prediction
    ];
  }

  public function set_stop_title($stop_title) {
    $this->stop_title = $stop_title;
  }

  public function set_epoch_time($epoch_time) {
    $this->epoch_time = $epoch_time;
  }

  public function set_route_tag($route_tag) {
    $this->route_tag = $route_tag;
  }

  public function set_direction($direction) {
    $this->direction = $direction;
  }

  public function set_no_prediction($no_prediction) {
    $this->no_prediction = $no_prediction;
  }

  public function get_stop_title($stop_title) {
    return $this->stop_title;
  }

  public function get_epoch_time($epoch_time) {
    return $this->epoch_time;
  }

  public function get_route_tag($route_tag) {
    return $this->route_tag;
  }

  public function get_direction($direction) {
    return $this->direction;
  }

  public function get_no_prediction($no_prediction) {
    return $this->no_prediction;
  }

}