<?php
App::uses('AppModel', 'App.Model');
App::uses('HttpSocket', 'Network/Http');
class GoogleCalculator extends AppModel {

	public $useTable = false;
	
	public $actsAs = array(
		'Containable'
	);

	protected $_defaultQuery = array(
		'hl' => 'en'
	);

	protected $_url = 'http://www.google.com/ig/calculator';

	public function conversion($source, $target) {
		$query = array($source, $target);
		$cacheKey = $this->_generateCacheKey('conversion', $query);
		if (($data = Cache::read($cacheKey)) === false) {
			$HttpSocket = new HttpSocket();
			$query = $this->_defaultQuery;
			$query['q'] = "$source=?$target";
			$results = $HttpSocket->get($this->_url, $query);
			if ($results->code != 200) {
				return false;
			}
			$data = $this->_decode($results);
			Cache::write($cacheKey, $data);
		}
		return $data;
	}

	protected function _decode($string) {
		$string = preg_replace(
			"/((\"?[^\"]+\"?)[ ]*:[ ]*([^,\"]+|\"[^\"]*\")(,?))/i", 
			'"\\2": \\3\\4', 
			str_replace(array('{', '}'), array('',''), $string)
		);
		$string = '{'.$string.'}';
		$string =  json_decode($string, true);
		return $string;
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
