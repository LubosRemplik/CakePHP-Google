<?php
App::uses('GoogleApi', 'Google.Model');
class GoogleCalendar extends GoogleApi {

	protected $_request = array(
		'method' => 'GET',
		'uri' => array(
			'scheme' => 'https',
			'host' => 'www.googleapis.com',
			'path' => '/calendar/v3/calendars',
		)
	);

	/**
	 * https://developers.google.com/google-apps/calendar/v3/reference/calendars/get
	 **/
	public function get($id, $options = array()) {
		$request = array();
		if ($options) {
			$request['uri']['query'] = $options;
		}
		return $this->_request(sprintf('/%s', $id), $request);
	}
}
