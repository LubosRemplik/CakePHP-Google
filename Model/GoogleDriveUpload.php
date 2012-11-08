<?php
App::uses('GoogleApi', 'Google.Model');
class GoogleDriveUpload extends GoogleApi {

	protected $_request = array(
		'method' => 'GET',
		'uri' => array(
			'scheme' => 'https',
			'host' => 'www.googleapis.com',
			'path' => '/upload/drive/v2',
		)
	);

	public function insertFile($file, $driveFile, $options = array()) {
		$path = sprintf('/files/%s', $driveFile['id']);
		$request = array();
		$request['method'] = 'PUT';
		$request['body'] = file_get_contents($file['tmp_name']);
		$request['header']['Content-Type'] = $file['type'];
		return $this->_request($path, $request);
	}
}
