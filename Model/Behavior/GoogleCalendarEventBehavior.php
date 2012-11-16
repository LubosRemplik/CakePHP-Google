<?php
class GoogleCalendarEventBehavior extends ModelBehavior {

	public function setup($model, $settings = array()) {
		if (empty($settings['calendarId'])) {
			$settings['calendarId'] = Configure::read('Google.Events.calendarId');
		}
		if (empty($settings['calendarId']) && class_exists('AppPreference')) {
			$settings['calendarId'] = AppPreference::get('Google.calendar_id');
		}
		if (!isset($this->settings[$model->alias])) {
			$this->settings[$model->alias] = array(
				'scope' => array(),
			);
		}
		$this->settings[$model->alias] = array_merge(
			$this->settings[$model->alias], 
			(array) $settings
		);
	}

	public function afterSave($model, $created) {
		$allowed = true;
		if (empty($model->data)) {
			$model->data = $model->read();
		}
		if (!empty($this->settings[$model->alias]['scope'])) {
			foreach ($this->settings[$model->alias]['scope'] as $field => $value) {
				if ($model->data[$model->alias][$field] != $value) {
					$allowed = false;
				}
			}
		}
		if (!$this->settings[$model->alias]['calendarId']) {
			$allowed = false;
		}
		if (empty($model->data[$model->alias]['title'])) {
			$allowed = false;
		}
		$startTime = false;
		$endTime = false;
		if (!empty($model->data[$model->alias]['date'])) {
			$startDate = $model->data[$model->alias]['date'];
			$endDate = strtotime($model->data[$model->alias]['date']) + 24 * 3600;
			$endDate = date('Y-m-d', $endDate);
		}
		if (empty($startDate) || empty($endDate)) {
			$allowed = false;
		}
		if (!$created && empty($model->data[$model->alias]['google_event_id'])) {
			$allowed = false;
		}
		if ($allowed) {
			$calendarId = $this->settings[$model->alias]['calendarId'];
			$data = array(
				'summary' => $model->data[$model->alias]['title'],
				'start' => array(
					'date' => $startDate,
				),
				'end' => array(
					'date' => $endDate,
				)
			);
			$Events = ClassRegistry::init('Google.GoogleCalendarEvents');
			if ($created) {
				$saved = $Events->insert($calendarId, $data);
				$model->id = $model->data[$model->alias]['id'];
				return $model->save(
					array('google_event_id' => $saved['id']),
					array('callbacks' => false)
				);
			} else {
				$eventId = $model->data['Event']['google_event_id'];
				$saved = $Events->update($calendarId, $eventId, $data);
			}
		}
		return true;
	}

	public function afterDelete($model) {
		$allowed = true;
		if (empty($model->data)) {
			$model->data = $model->read();
		}
		if (!$this->settings[$model->alias]['calendarId']) {
			$allowed = false;
		}
		if (empty($model->data[$model->alias]['google_event_id'])) {
			$allowed = false;
		}
		if ($allowed) {
			$Events = ClassRegistry::init('Google.GoogleCalendarEvents');
			$calendarId = $this->settings[$model->alias]['calendarId'];
			$eventId = $model->data[$model->alias]['google_event_id'];
			$Events->delete($calendarId, $eventId);
		}
	}
}
