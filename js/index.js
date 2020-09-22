function showGraph(){
	g = new Dygraph(
		document.getElementById("graph"),
		"csv.php", // path to CSV file
			{
			labels: ['Data', 'Ambiente', 'CPU', 'GPU'],
			labelsSeparateLines: true,
			series : {
				'Ambiente': {
					fillGraph: true,
					fillAlpha: 0.8,
					color: 'red',
					drawPoints: true,
					pointSize: 2,
					highlightCircleSize: 6,
				},
				'CPU': {
					color: 'blue',
					drawPoints: true,
					pointSize: 2,
					highlightCircleSize: 6,
				},
				'GPU': {
					color: 'grey',
					drawPoints: true,
					pointSize: 2,
					highlightCircleSize: 6,
				}
			},
			title: 'Javelin',
			titleHeight: 32,
			ylabel: 'Temperatura (ºC)',
			xlabel: 'Data',
			strokeWidth: 1.5,
		} // options
	);
	
	return g;
}

function currentTemperature(){
	var temp = [];
	
	$.post(
		'current-temperature-json.php',
		function(data){
			temp[0] = data[0]['last'];
			temp[1] = data[0]['max'];
			temp[2] = data[0]['min'];			
		}
	);
	
	return temp;
}

function showThermometer(){
	var data = currentTemperature();
	
	th = new RGraph.Thermometer({
		id: 'thermometer',
		min: data[2],
		max: data[1],
		value: data[0],
		options: {
			colors: ['Gradient(#c00:red:#f66:#fcc)'],
			labelsValueDecimals: 2,
			labelsValuePoint: ',',
			labelsValueUnitsPost: 'ºC',
			scaleVisible: true,
			scalePoint: ',',
			scaleDecimals: 2,
			scaleUnitsPost: 'ºC',
			titleSide: 'Temperatura atual',
		}
	});
	
	return th;
}

$(function(){
	showThermometer().draw();
	
	var g = showGraph();

	window.intervalId = setInterval(function() {
			g.updateOptions( { 'file': "csv.php" } );
			showThermometer().draw();
			
		}, 300000);
});