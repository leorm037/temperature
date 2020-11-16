 <?php

require_once('db.php');

header('Content-Type: text/csv');
header('Content-Transfer-Encoding: UTF-8');
header('Content-Disposition: inline; filename=temperatura.csv');
header('Pragma: no-cache');
header('Expires: 0');

$sql = "SELECT timestamp, environment, cpu, gpu, external, external_sensation FROM temperature WHERE scale = 'C' ORDER BY timestamp DESC LIMIT 300";

$stmt = $pdo->query($sql);

$temperatures = $stmt->fetchAll();

//echo "timestamp,environment,cpu,gpu\n";

foreach($temperatures as $temperature){
	$timestamp = str_replace("-","/",$temperature['timestamp']);
	$tempEnvironment = (float)$temperature['environment'];
	$tempCpu = (float)$temperature['cpu'];
	$tempGpu = (float)$temperature['gpu'];
	$tempExternal = (float)$temperature['external'] <> 0 ? (float)$temperature['external'] : 'null';
	$tempSensation = (float)$temperature['external_sensation'] <> 0 ? (float)$temperature['external_sensation'] : 'null';
	

	echo $timestamp . "," . $tempEnvironment . "," .$tempCpu . "," . $tempGpu . "," . $tempExternal . "," . $tempSensation . "\n";
}

$pdo = null;

