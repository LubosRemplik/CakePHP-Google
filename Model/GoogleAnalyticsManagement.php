<?php
App::uses('GoogleApi', 'Google.Model');
class GoogleAnalyticsManagement extends GoogleApi {

	protected $_request = array(
		'method' => 'GET',
		'uri' => array(
			'scheme' => 'https',
			'host' => 'www.googleapis.com',
			'path' => '/analytics/v3/management',
		)
	);

	/**
	 * https://developers.google.com/analytics/devguides/config/mgmt/v3/mgmtReference/management/accounts/list
	 **/
	public function accounts($options = array()) {
		$request = array();
		if ($options) {
			$request['uri']['query'] = $options;
		}
		return $this->_request('/accounts', $request);
	}

	/**
	 * https://developers.google.com/analytics/devguides/config/mgmt/v3/mgmtReference/management/profiles/list
	 **/
	public function profiles($accountId, $webPropertyId, $options = array()) {
		$path = sprintf('/accounts/%s/webproperties/%s/profiles', $accountId, $webPropertyId);
		$request = array();
		if ($options) {
			$request['uri']['query'] = $options;
		}
		return $this->_request($path, $request);
	}

	/**
	 * https://developers.google.com/analytics/devguides/config/mgmt/v3/mgmtReference/management/webproperties/list
	 **/
	public function webproperties($accountId, $options = array()) {
		$path = sprintf('/accounts/%s/webproperties', $accountId);
		$request = array();
		if ($options) {
			$request['uri']['query'] = $options;
		}
		return $this->_request($path, $request);
	}
}
