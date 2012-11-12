<?php
App::uses('GoogleApi', 'Google.Model');
class GoogleCalendarList extends GoogleApi {

	protected $_request = array(
		'method' => 'GET',
		'uri' => array(
			'scheme' => 'https',
			'host' => 'www.googleapis.com',
			'path' => '/calendar/v3/users/me/calendarList',
		)
	);

	/**
	 * https://developers.google.com/google-apps/calendar/v3/reference/calendarList/list
	 **/
	public function listItems($options = array()) {
		$request = array();
		if ($options) {
			$request['uri']['query'] = $options;
		}
		return $this->_request(null, $request);
	}

}
