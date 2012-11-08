<?php
App::uses('GoogleApi', 'Google.Model');
class Google extends GoogleApi {

	protected $_request = array(
		'method' => 'GET',
		'uri' => array(
			'scheme' => 'https',
			'host' => 'www.googleapis.com',
			'path' => '/oauth2/v2',
		)
	);

	public function getUserInfo() {
		return $this->_request('/userinfo');
	}
}
