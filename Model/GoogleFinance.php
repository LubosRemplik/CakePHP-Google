<?php
App::uses('AppModel', 'App.Model');
App::uses('HttpSocket', 'Network/Http');
class GoogleFinance extends AppModel {

	public $useTable = false;
	
	public $actsAs = array(
		'Containable'
	);

	protected $_url = 'https://www.google.com/finance/converter';

	public function converter($source, $target, $amount = 1) {
		$query = array($source, $target);
		$cacheKey = $this->_generateCacheKey('converter', $query);
		if (($data = Cache::read($cacheKey)) === false) {
			$get = sprintf(
				'%s?a=%s&from=%s&to=%s',
				$this->_url,
				urlencode($amount),
				urlencode($source),
				urlencode($target)
			);
			$get = file_get_contents($get);
			$get = explode("<span class=bld>",$get);
			$get = explode("</span>",$get[1]);  
			$data = preg_replace("/[^0-9\.]/", null, $get[0]);
			if (!empty($data)) {
				Cache::write($cacheKey, $data);
			}
		}
		return $data;
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
