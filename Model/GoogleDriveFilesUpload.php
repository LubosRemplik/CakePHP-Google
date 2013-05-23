<?php
App::uses('GoogleApi', 'Google.Model');
App::uses('CakeResponse', 'Network');
class GoogleDriveFilesUpload extends GoogleApi {

	protected $_request = array(
		'method' => 'PUT',
		'uri' => array(
			'scheme' => 'https',
			'host' => 'www.googleapis.com',
			'path' => '/upload/drive/v2/files',
		)
	);

	/**
	 * https://developers.google.com/drive/v2/reference/files/insert
	 **/
	public function insertFile($file, $driveFile, $options = array()) {
		// setting default options
		$options = array_merge(
			array('convert' => 'true'),
			$options
		);

		// seting path and request
		$path = sprintf('/%s', $driveFile['id']);
		$request = array();
		$request['uri']['query'] = $options;
		$request['body'] = file_get_contents($file['tmp_name']);

		// using CakeReponse to guess mime type
		$ext = array_pop(explode('.', $file['name']));
		$CR = new CakeResponse();
		$request['header']['Content-Type'] = $CR->getMimeType($ext);
		return $this->_request($path, $request);
	}


}
