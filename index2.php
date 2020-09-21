<!doctype html>
<html lang="pt_BR">
	<head>
		<title>Temperatura</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta http-equiv="refresh" content="300">
		<link href="css/style.css" rel="stylesheet" type="text/css"/>
		<script src="js/jquery.min.js"></script>
		<script src="js/moment.min.js"></script>
		<script src="js/Chart.min.js"></script>
	</head>
	<body>
		<div id="chart-container">
			<canvas id="graphCanvas"></canvas>
		</div>
		<script>
			console.log(moment().format());

			$(document).ready(function(){
				showGraph();
			});

			function showGraph(){
				{
					$.post(
						"json.php",
						function(data){
							var timestamp = [];
							var environment = [];
							var cpu = [];
							var gpu = [];

							for(var i in data){
								timestamp.push(moment(data[i][0]).format("DD/MM/YYYY HH:mm:ss"));
								environment.push(data[i][1]);
								cpu.push(data[i][2]);
								gpu.push(data[i][3]);
							}

							//console.group("Timestamp");
							//console.log(timestamp);
							//console.group("Environment");
							//console.log(environment);
							//console.group("CPU");
							//console.log(cpu);
							//console.group("GPU");
							//console.log(gpu);

							var chartdata = {
								labels: timestamp,
								datasets: [
									{
										label: "Ambiente (ºC)",
										backgroundColor: 'rgb(255,99,132)',
										borderColor: 'rgb(255,99,132)',
										fill: true,
										data: environment
									},
									{
										label: "CPU (ºC)",
										backgroundColor: 'rgb(54,162,235)',
										borderColor: 'rgb(54,162,235)',
										fill: false,
										data: cpu

									},
									{
										label: "GPU (ºC)",
										borderDash: [5,5],
										backgroundColor: 'rgb(152,102,255)',
										borderColor: 'rgb(152,102,255)',
										fill: false,
										data: gpu

									}
								]
							};

							var graphTarget = $("#graphCanvas");

							var barGraph = new Chart(
								graphTarget, 
								{
									type: 'line',
									data: chartdata,
									options: {
										responsive: true,
										title: {
											display: true,
											text: 'Javelin',
											fontSize: 24,
										},
										tooltips: {
											mode: 'index',
											intersect: false,
										},
										hover: {
											mode: 'nearest',
											intersect: true
										},
										scales: {
											xAxes: [{
												display: true,
												scaleLabel: {
													display: true,
													labelString: 'Data'
												}
											}],
											yAxes: [{
												display: true,
												scaleLabel: {
													display: true,
													labelString: 'Temperatura (ºC)'
												}
											}]
										}
									},
								}
							);
						}
					);
				}
			}
		</script>
	</body>
</html>
