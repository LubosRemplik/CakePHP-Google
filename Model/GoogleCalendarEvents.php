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
	 * https://developers.google.com/drive/v2/reference/files/delete
	 **/
	public function delete($calendarId, $eventId, $options = array()) {
		$path = sprintf('/%s/events/%s', $calendarId, $eventId);
		$request = array();
		$request['method'] = 'DELETE';
		if ($options) {
			$request['uri']['query'] = $options;
		}
		return $this->_request($path, $request);
	}

	/**
	 * list items between $from and $to and then delete them
	 **/
	public function deleteAll($from, $to, $calendarId, $options = array()) {
		$deletedAll = true;
		$listOptions = array(
			'timeMin' => date('c', strtotime($from)),
			'timeMax' => date('c', strtotime($to))
		);
		Cache::clear();
		$events = $this->listItems($calendarId, $listOptions);
		while (!empty($events['items']) && count($events['items']) > 0) {
			foreach ($events['items'] as $item) {
				$deleted = $this->delete($calendarId, $item['id']);
				if (!empty($deleted)) {
					$deletedAll = false;
				}
			}
			Cache::clear();
			$events = $this->listItems($calendarId, $listOptions);
		}
		return $deletedAll;
	}

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

	/**
	 * https://developers.google.com/google-apps/calendar/v3/reference/events/update
	 *
	 * Similar to insert method
	 **/
	public function update($calendarId, $eventId, $data, $options = array()) {
		$path = sprintf('/%s/events/%s', $calendarId, $eventId);
		$request = array();
		$request['method'] = 'PUT';
		if ($options) {
			$request['uri']['query'] = $options;
		}
		$request['body'] = json_encode($data);
		$request['header']['Content-Type'] = 'application/json';
		return $this->_request($path, $request);
	}
}
