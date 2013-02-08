<?php
App::uses('AppHelper', 'View/Helper');
class GoogleChartHelper extends AppHelper {

	public $helpers = array(
		'Html',
		'Js'
	);

	public function __construct(View $View, $settings = array()) {
		parent::__construct($View, $settings);
		$this->Html->script(
			'https://www.google.com/jsapi', 
			array('inline' => false)
		);
	}
	
	public function pieChart($data, $options = array(), $htmlAttributes = array()) {
		// setting default options
		$options = array_merge(
			array(
				'chartArea' => array(
					'top' => '5%',
					'width' => '90%', 
					'height' => '90%'
				),
			),
			$options
		);

		$output = $this->_chart('PieChart', $data, $options, $htmlAttributes);
		return $output;
	}

	public function lineChart($data, $options = array(), $htmlAttributes = array()) {
		$output = $this->_chart('LineChart', $data, $options, $htmlAttributes);
		return $output;
	}

	protected function _chart($type, $data, $options, $htmlAttributes) {
		// basic settings
		if (empty($htmlAttributes['id'])) {
			$htmlAttributes['id'] = sprintf('%s%s', $type, md5(serialize($data)));
		}
		$element = $htmlAttributes['id'];

		// transforming data into JS
		$data = json_encode($data);
		$options = json_encode((object)$options);

		// creating script block
		$script = '';
		$script .= "google.load('visualization', '1.0', {'packages':['corechart']});";
		$script .= "google.setOnLoadCallback(function() {draw%s('%s', %s, %s)});";
		$script = sprintf($script, $type, $element, $data, $options);

		// output 
		$output = '';
		$output .= $this->Html->scriptBlock($script);
		$output .= $this->Html->div(null, '', $htmlAttributes);
		return $output;
	}
}
