<?php
if(!isset($width)) $width = 640;
if(!isset($height)) $height = 640;
if(!isset($lat)) $lat = '51.476752';
if(!isset($lng)) $lng = 0;
if(!isset($zoom)) $zoom = 13;
if(!isset($type)) $type = 'ROADMAP';
if(!isset($content)) $content = $this->Html->tag('strong', 'Greenwich');
echo $this->Html->div(null, '', array('id'=>'map_canvas', 'style'=>"width: {$width}px; height: {$height}px"));
echo $this->Html->scriptBlock(
	"function initialize() {
		var myLatlng = new google.maps.LatLng($lat, $lng);
		var myOptions = {
			zoom: $zoom,
			center: myLatlng,
			mapTypeId: google.maps.MapTypeId.$type
		}
		var map = new google.maps.Map(document.getElementById('map_canvas'), myOptions);
		var infoWindow;
		infoWindow = new google.maps.InfoWindow();
	    infoWindow.setContent(\"$content\");
	    infoWindow.setPosition(myLatlng);
	    infoWindow.open(map);
	}
	
	function loadScript() {
		var script = document.createElement('script');
		script.type = 'text/javascript';
		script.src = 'http://maps.google.com/maps/api/js?sensor=false&callback=initialize';
		document.body.appendChild(script);
	}
	
	window.onload = loadScript;",
	array('inline'=>false)
);