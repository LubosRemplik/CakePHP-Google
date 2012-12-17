<?php
App::uses('GoogleMapsApi', 'Google.Model');
class GoogleGeocoding extends GoogleMapsApi {

	protected $_request = array(
		'method' => 'GET',
		'uri' => array(
			'scheme' => 'https',
			'host' => 'maps.googleapis.com',
			'path' => 'maps/api/geocode/json',
		)
	);

	public function get($address, $options = array()) {
		$request = array();
		$query = array(
			'address' => $address,
			'sensor' => 'false'
		);
		if ($options) {
			$query = array_merge($query, $options);
		}
		$request['uri']['query'] = $query;
		return $this->_request(null, $request);
	}
}
