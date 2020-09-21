<?php

require_once('db.php');

header('Content-Type: application/json');

$sql = "SELECT timestamp, environment, cpu, gpu FROM temperature WHERE scale = 'C' ORDER BY timestamp ASC";

$stmt = $pdo->query($sql);

$temperatures = $stmt->fetchAll();

foreach($temperatures as $temperature){
	$timestamp = strtotime($temperature['timestamp'])*1000;
	$tempEnvironment = (float)$temperature['environment'];
	$tempCpu = (float)$temperature['cpu'];
	$tempGpu = (float)$temperature['gpu'];

	$data[] = array($timestamp,$tempEnvironment,$tempCpu,$tempGpu);
}

echo json_encode($data);

$pdo = null;

