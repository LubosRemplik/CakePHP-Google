<?php
App::uses('AppModel', 'Model');
App::uses('Hash', 'Utility');
App::uses('HttpSocket', 'Network/Http');
App::uses('Set', 'Utility');
class GoogleMapsApi extends AppModel {

	public $useTable = false;
	
	protected $_request = array(
		'method' => 'GET',
		'uri' => array(
			'scheme' => 'https',
			'host' => 'maps.googleapis.com',
		)
	);

	protected $_strategy = 'Google';

	protected function _generateCacheKey() {
		$backtrace = debug_backtrace();
		$cacheKey = array();
		$cacheKey[] = $this->alias;
		if (!empty($backtrace[2]['function'])) {
			$cacheKey[] = $backtrace[2]['function'];
		}
		if ($backtrace[2]['args']) {
			$cacheKey[] = md5(serialize($backtrace[2]['args']));	
		}
		return implode('_', $cacheKey);
	}

	protected function _parseResponse($response) {
		$results = json_decode($response->body);
		if (is_object($results)) {
			$results = Set::reverse($results);
		}
		return $results;
	}

	protected function _request($path, $request = array()) {
		// preparing request
		$request = Hash::merge($this->_request, $request);
		$request['uri']['path'] .= $path;
		// Read cached GET results
		if ($request['method'] == 'GET') {
			$cacheKey = $this->_generateCacheKey();
			$results = Cache::read($cacheKey);
			if ($results !== false) {
				return $results;
			}
		}

		// createding http socket object for later use
		$HttpSocket = new HttpSocket();

		// issuing request
		$response = $HttpSocket->request($request);

		// olny valid response is going to be parsed
		if (substr($response->code, 0, 1) != 2) {
			if (Configure::read('debugApis')) {
				debug($request);
				debug($response->body);
			}
			return false;
		}

		// parsing response
		$results = $this->_parseResponse($response);

		// cache and return results
		if ($request['method'] == 'GET') {
			Cache::write($cacheKey, $results);
		}
		return $results;
	}
}
