<?php
App::uses('GoogleApi', 'Google.Model');
class GoogleDriveFilesUpload extends GoogleApi {

	protected $_request = array(
		'method' => 'POST',
		'uri' => array(
			'scheme' => 'https',
			'host' => 'www.googleapis.com',
			'path' => '/upload/drive/v2/files',
		)
	);

	/**
	 * https://developers.google.com/drive/v2/reference/files/insert
	 **/
	public function insert($file, $driveFile, $options = array()) {
		$path = sprintf('/%s', $driveFile['id']);
		$request = array();
		$request['body'] = file_get_contents($file['tmp_name']);
		$request['header']['Content-Type'] = $file['type'];
		return $this->_request($path, $request);
	}
}
