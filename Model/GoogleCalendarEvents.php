<?php
App::uses('GoogleApi', 'Google.Model');
class GoogleCalendarEvents extends GoogleApi {

	protected $_request = array(
		'method' => 'GET',
		'uri' => array(
			'scheme' => 'https',
			'host' => 'www.googleapis.com',
			'path' => '/calendar/v3/calendars',
		)
	);

	/**
	 * https://developers.google.com/google-apps/calendar/v3/reference/events/insert
	 *
	 * $calendarId the calendar you want to insert event
	 * $data represents request body (start, end properties are required)
	 * $options are parameters
	 **/
	public function insert($calendarId, $data, $options = array()) {
		$request = array();
		$request['method'] = 'POST';
		if ($options) {
			$request['uri']['query'] = $options;
		}
		$request['body'] = json_encode($data);
		$request['header']['Content-Type'] = 'application/json';
		return $this->_request(sprintf('/%s/events', $calendarId), $request);
	}

	/**
	 * https://developers.google.com/google-apps/calendar/v3/reference/events/list
	 **/
	public function listItems($calendarId, $options = array()) {
		$request = array();
		if ($options) {
			$request['uri']['query'] = $options;
		}
		return $this->_request(sprintf('/%s/events', $calendarId), $request);
	}
}
