function showGraph(){
	return new Dygraph(
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
}

function showThermometer(){
	$.getJSON(
		'current-temperature-json.php',
		function(data){
			var t = new RGraph.Thermometer({
				id: 'thermometer',
				min: data[0]['min'],
				max: data[0]['max'],
				value: data[0]['last'],
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

			RGraph.clear(document.getElementById('thermometer'));

			t.draw();
		}
	);
}

var g = showGraph();
showThermometer();

$(function(){
	window.intervalId = setInterval(function() {
			g.updateOptions( { 'file': "csv.php" } );
			showThermometer();
		}, 300000);
});
