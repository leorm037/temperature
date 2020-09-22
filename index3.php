<html lang="pt_BR">
	<head>
		<title>Temperatura</title>
		<script src="js/jquery.min.js"></script>
		<script src="js/dygraph.min.js"></script>
		<script src="js/RGraph.common.core.js"></script>
		<script src="js/RGraph.thermometer.js"></script>
		<link rel="stylesheet" type="text/css" href="css/dygraph.css" />
		<link rel="stylesheet" type="text/css" href="css/index.css" />
	</head>
	<body>
		<div id="graph"></div>
		<div><canvas id="thermometer" width="120" height="400">[No canvas support]</canvas></div>
		<script type="text/javascript">
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

			t = $.post(
				'current-temperature-json.php',
				function(data){
					var temp_current = data[0]['last'];
					var temp_max = data[0]['max'];
					var temp_min = data[0]['min'];

					t = new RGraph.Thermometer({
						id: 'thermometer',
						min: temp_min,
						max: temp_max,
						value: temp_current,
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
					}).draw();
				}
			);

			$(function(){
				t();
		
				window.intervalId = setInterval(function() {
        				g.updateOptions( { 'file': "csv.php" } );
     				}, 300000);
			});
		</script>
 	</body>
</html>
