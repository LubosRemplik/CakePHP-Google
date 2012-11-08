<?php
App::uses('GoogleApi', 'Google.Model');
class GoogleDrive extends GoogleApi {

	public $hasOne = array('Google.GoogleDriveUpload');

	protected $_request = array(
		'method' => 'GET',
		'uri' => array(
			'scheme' => 'https',
			'host' => 'www.googleapis.com',
			'path' => '/drive/v2',
		)
	);

	public function insertFile($file, $options = array()) {
		$request = array();
		$request['method'] = 'POST';
		$request['uri']['query'] = $options;
		$body = array(
			'title' => $file['name'],
			'mimeType' => $file['type']
		);
		$request['body'] = json_encode($body);
		$request['header']['Content-Type'] = 'application/json';
		return $this->GoogleDriveUpload->insertFile(
			$file, $this->_request('/files', $request),
			$options
		);
	}

	public function listFiles($options = array()) {
		$request = array();
		if ($options) {
			$request['uri']['query'] = $options;
		}
		return $this->_request('/files', $request);
	}
}
