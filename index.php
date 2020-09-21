<html lang="pt_BR">
	<head>
		<title>Temperatura</title>
		<script src="js/jquery.min.js"></script>
		<script src="js/dygraph.min.js"></script>
		<link rel="stylesheet" type="text/css" href="css/dygraph.css" />
		<link rel="stylesheet" type="text/css" href="css/index.css" />
	</head>
	<body>
		<div id="graph"></div>
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
			$(document).ready(function(){
				window.intervalId = setInterval(function() {
        				g.updateOptions( { 'file': "csv.php" } );
     				}, 300000);
			});
		</script>
 	</body>
</html>
/
