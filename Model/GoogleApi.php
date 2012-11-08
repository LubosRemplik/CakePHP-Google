<?php
App::uses('AppModel', 'App.Model');
App::uses('CakeSession', 'Model/Datasource');
App::uses('Hash', 'Utility');
App::uses('HttpSocket', 'Network/Http');
App::uses('Set', 'Utility');
class GoogleApi extends AppModel {

	public $useTable = false;
	
	protected $_config = array();

	protected $_request = array(
		'method' => 'GET',
		'uri' => array(
			'scheme' => 'https',
			'host' => 'www.googleapis.com',
		)
	);

	protected $_strategy = 'Google';

	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
		$session = CakeSession::read($this->_strategy);
		$configure = Configure::read(sprintf(
			'Opauth.Strategy.%s', 
			$this->_strategy
		));
		if (!empty($session) && !empty($configure)) {
			$this->_config = array_merge($session, $configure);
		}
	}

	protected function _parseResponse($response) {
		$results = json_decode($response->body);
		if (is_object($results)) {
			$results = Set::reverse($results);
		}
		return $results;
	}

	protected function _request($path, $request = array()) {
		// createding http socket object for later use
		$HttpSocket = new HttpSocket();

		// checking access token expires time, using refresh token when needed
		$date = date('c', time());
		if($date > $this->_config['credentials']['expires'] 
		& isset($this->_config['credentials']['refresh_token'])) {

			// getting new credentials
			$requestRefreshToken = array(
				'method' => 'POST',
				'uri' => array(
					'scheme' => 'https',
					'host' => 'accounts.google.com',
					'path' => '/o/oauth2/token',
				),
				'body' => sprintf(
					'client_id=%s&client_secret=%s&refresh_token=%s&grant_type=refresh_token',
					$this->_config['client_id'],
					$this->_config['client_secret'],
					$this->_config['credentials']['refresh_token']
				),
				'header' => array(
					'Content-Type' => 'application/x-www-form-urlencoded'
				)
			);
			$response = $HttpSocket->request($requestRefreshToken);
			if ($response->code != 200) {
				if (Configure::read('debug')) {
					debug($requestRefreshToken);
					debug($response->body);
				}
				return false;
			}
			$results = $this->_parseResponse($response);
			$credentials = array(
				'token' => $results['access_token'],
				'expires' => date('c', time() + $results['expires_in'])
			);

			// saving new credentials
			$this->_config['credentials'] = array_merge(
				$this->_config['credentials'],
				$credentials
			);
			CakeSession::write(sprintf('%s.credentials', $this->_strategy), $this->_config['credentials']);
		}

		// preparing request
		$request = Hash::merge($this->_request, $request);
		$request['uri']['path'] .= $path;
		$request['header']['Authorization'] = sprintf('OAuth %s', $this->_config['credentials']['token']);

		// issuing request
		$response = $HttpSocket->request($request);

		// olny valid response is going to be parsed
		if ($response->code != 200) {
			if (Configure::read('debug')) {
				debug($request);
				debug($response->body);
			}
			return false;
		}

		// parsing response
		$results = $this->_parseResponse($response);

		// return results
		return $results;
	}
}
