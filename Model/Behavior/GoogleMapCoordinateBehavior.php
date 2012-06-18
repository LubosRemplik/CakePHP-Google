<?php
/*
 * Retrieves latitude and longitude from Google using information in passed data array. 
 * Subsequently adds retruned coordinates to passed data array.
 */
class GoogleMapCoordinateBehavior extends ModelBehavior {
	
	public function setup($model, $settings = array()) {
		$settings = (array) $settings;
		
		$settings = am(array(
			'latitudeField' => 'latitude',
			'longitudeField' => 'longitude',
			'addressField' => 'address',
			'postfix' => null
		), $settings);
		
		$this->settings[$model->alias] = $settings;
	}
	
	function beforeSave($model) {
				
		$address = array($this->settings[$model->alias]['postfix']);
		if (isset($this->settings[$model->alias]['addressField'])) {
			$address[] = str_replace(array("\r\n", "\r"), ', ', $model->data[$model->alias][$this->settings[$model->alias]['addressField']]);
			$address = array_reverse(array_filter($address));
		}
		$address = implode(', ', $address);
		
		$outputLoc = @explode(',', file_get_contents('http://maps.google.com/maps/geo?q='.urlencode($address).'&output=csv&sensor=false'));
		
		$model->data[$model->alias][$this->settings[$model->alias]['latitudeField']] = $outputLoc[2];
		$model->data[$model->alias][$this->settings[$model->alias]['longitudeField']] = $outputLoc[3];
		
		return true;
	}
}