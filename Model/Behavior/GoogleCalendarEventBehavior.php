<?php
class GoogleCalendarEventBehavior extends ModelBehavior {

	public function setup(Model $model, $settings = array()) {
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

	public function afterSave(Model $model, $created, $options = array()) {
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
		if ($allowed && !empty($model->data[$model->alias]['date'])) {
			$startDate = strtotime($model->data[$model->alias]['date']);
			$startDate = date('Y-m-d', $startDate);
			if (!empty($model->data[$model->alias]['date_to'])) {
				$endDate = strtotime($model->data[$model->alias]['date_to']) + 24 * 3600;
				$endDate = date('Y-m-d', $endDate);
			} else {
				$endDate = strtotime($model->data[$model->alias]['date']) + 24 * 3600;
				$endDate = date('Y-m-d', $endDate);
			}
		}
		if ($allowed && !empty($model->data[$model->alias]['days'])) {
			date_default_timezone_set('UTC');
			$until = gmdate("Ymd\THis\Z", strtotime($model->data[$model->alias]['date_to']));
			$byDay = implode(',', $model->data[$model->alias]['days']);
			$recurrence = array(sprintf('RRULE:FREQ=WEEKLY;UNTIL=%s;BYDAY=%s', $until, $byDay));
			$endDate = $startDate;
		}
		if (empty($startDate) || empty($endDate)) {
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
			if (!empty($recurrence)) {
				$data['recurrence'] = $recurrence;
			}
			if (!empty($model->data[$model->alias]['content'])) {
				$description = $model->data[$model->alias]['content'];
				$description = str_replace('<br />', PHP_EOL, $description);
				$description = strip_tags($description);
				$data['description'] = $description;
			}
			if (!empty($model->data[$model->alias]['location'])) {
				$location = $model->data[$model->alias]['location'];
				$location = strip_tags($location);
				$data['location'] = $location;
			}
			if (!empty($model->data[$model->alias]['color_id'])) {
				$data['colorId'] = $model->data[$model->alias]['color_id'];
			}
			$Events = ClassRegistry::init('Google.GoogleCalendarEvents');
			if (empty($model->data[$model->alias]['google_event_id'])) {
				$saved = $Events->insert($calendarId, $data);
				$model->id = $model->data[$model->alias]['id'];
				return $model->saveField('google_event_id', $saved['id']);
			} else {
				$eventId = $model->data['Event']['google_event_id'];
				$data['sequence'] = $model->data['Event']['google_event_sequence'] + 1;
				$saved = $Events->update($calendarId, $eventId, $data);
				if ($saved) {
					$model->id = $model->data[$model->alias]['id'];
					$model->Behaviors->disable('GoogleCalendarEvent');
					return $model->saveField('google_event_sequence', $data['sequence']);
				}
			}
		} else if (!$allowed && !empty($model->data[$model->alias]['google_event_id'])) {
			$deleteAllowed = true;
			if (!$this->settings[$model->alias]['calendarId']) {
				$deleteAllowed = false;
			}
			if (empty($model->data[$model->alias]['google_event_id'])) {
				$deleteAllowed = false;
			}
			if ($deleteAllowed) {
				$Events = ClassRegistry::init('Google.GoogleCalendarEvents');
				$calendarId = $this->settings[$model->alias]['calendarId'];
				$eventId = $model->data[$model->alias]['google_event_id'];
				$Events->delete($calendarId, $eventId);
			}
		}
		return true;
	}

	public function afterDelete(Model $model) {
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
