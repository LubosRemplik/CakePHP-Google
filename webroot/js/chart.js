function drawPieChart(element, rows, options) {
	var data = new google.visualization.DataTable();
	data.addColumn('string', 'Key');
	data.addColumn('number', 'Value');
	data.addRows(rows);	
	var chart = new google.visualization.PieChart(document.getElementById(element));
	chart.draw(data, options)
}

function drawLineChart(element, data, options) {
	var data = google.visualization.arrayToDataTable(data);
	var chart = new google.visualization.LineChart(document.getElementById(element));
	console.log(options);
	chart.draw(data, options);
}
