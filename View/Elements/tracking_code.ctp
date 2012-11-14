<?php
if (empty($webPropertyId)) {
	return false;
}
// TODO: implement custom vars and events tracking
$gaCustomVarTemplate = "  _gaq.push(['_setCustomVar', %s, '%s', '%s', %s]);";
$gaEventTemplate = "  _gaq.push(['_trackEvent', '%s', '%s', '%s']);";
$events = '';
$customVars = '';
$trackingCode = 
	"var _gaq = _gaq || [];
	_gaq.push(['_setAccount', '$webPropertyId']);
	$events
	$customVars
	_gaq.push(['_trackPageview']);

	(function() {
		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	})();";
$trackingCode = $this->Html->scriptBlock($trackingCode);
echo $trackingCode;
