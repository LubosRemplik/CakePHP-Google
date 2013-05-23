<?php
App::uses('GoogleApi', 'Google.Model');
class GoogleAnalyticsReporting extends GoogleApi {

	protected $_request = array(
		'method' => 'GET',
		'uri' => array(
			'scheme' => 'https',
			'host' => 'www.googleapis.com',
			'path' => '/analytics/v3/data/ga',
		)
	);

	/**
	 * https://developers.google.com/analytics/devguides/reporting/core/dimsmets
	 **/
	public function request($ids, $startDate, $endDate, $metrics, $dimensions = array(), $options = array()) {
		$request = array();
		$request['uri']['query'] = array(
			'ids' => $ids,
			'start-date' => $startDate,
			'end-date' => $endDate,
			'metrics' => implode(',', $metrics)
		);
		if (!empty($dimensions)) {
			$request['uri']['query']['dimensions'] = implode(',', $dimensions);
		}
		if (!empty($options)) {
			$request['uri']['query'] = array_merge(
				$options,
				$request['uri']['query']
			);
		}
		return $this->_request(null, $request);
	}
}
