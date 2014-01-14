<?php
App::uses('GoogleApi', 'Google.Model');
App::uses('Hash', 'Utility');
class GoogleCalendarColors extends GoogleApi {

	protected $_request = array(
		'method' => 'GET',
		'uri' => array(
			'scheme' => 'https',
			'host' => 'www.googleapis.com',
			'path' => '/calendar/v3/colors',
		)
	);

	protected $_colorsReadable = array(
		1 => 'blue',
		2 => 'green',
		3 => 'purple',
		4 => 'red',
		5 => 'yellow',
		6 => 'orange',
		7 => 'turquoise',
		8 => 'gray',
		9 => 'blue bold',
		10 => 'green bold',
		11 => 'red bold',
	);

	/**
	 * https://developers.google.com/google-apps/calendar/v3/reference/colors/get
	 **/
	public function get() {
		$request = array();
		return $this->_request(null, $request);
	}

	/**
	 * Format list of calendar colors
	 **/
	public function getCalendarColors() {
		$colors = $this->get();
		$results = array();
		foreach ($colors['calendar'] as $id => $set) {
			$results[$id] = $set['background'];
		}
		return $results;
	}

	/**
	 * Format list of event colors, human readable
	 **/
	public function getEventColors($raw = false) {
		$colors = $this->get();
		$results = array();
		foreach ($colors['event'] as $id => $set) {
			if ($raw) {
				$results[$id] = $set['background'];
			} else {
				$results[$id] = $this->_colorsReadable[$id];
			}
		}
		return $results;
	}
}
