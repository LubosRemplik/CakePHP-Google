<?php
App::uses('AppModel', 'App.Model');
App::uses('HttpSocket', 'Network/Http');
App::uses('Xml', 'Utility');
class GoogleWeather extends AppModel {

	public $useTable = false;
	
	public $actsAs = array(
		'Containable'
	);

	protected $_defaultQuery = array(
		'hl' => 'en'
	);

	protected $_url = 'http://www.google.com/ig/api';

	public function getCurrentConditions($place) {
		$data = $this->get($place);
		if(!$data) {
			return false;
		}
		$data = Set::extract($data, '/xml_api_reply/weather/current_conditions');
		$results = array();
		foreach ($data as $item) {
			$key = key($item);
			$value = $item[$key]['@data'];
			$results[$key] = $value;
		}
		return $results;
	}

	public function get($place) {
		$cacheKey = $this->_generateCacheKey('get', $place);
		if (($data = Cache::read($cacheKey)) === false) {
			$HttpSocket = new HttpSocket();
			$query = $this->_defaultQuery;
			$query['weather'] = $place;
			$results = $HttpSocket->get($this->_url, $query);
			if ($results->code != 200) {
				return false;
			}
			$data = $this->_decode($results->body);
			Cache::write($cacheKey, $data);
		}
		return $data;
	}

	protected function _decode($xml) {
		$xml = Xml::build($xml);
		$xml = Xml::toArray($xml);
		return $xml;
	}

	protected function _generateCacheKey($fceName, $query) {
		$cacheKey = array();
		$cacheKey[] = $this->alias;
		$cacheKey[] = $fceName;
		if ($query) {
			$cacheKey[] = md5(serialize($query));	
		}
		return implode('_', $cacheKey);
	}
}
