<?php

if (PHP_SAPI !== 'cli' || isset($_SERVER['HTTP_USER_AGENT'])) {
	echo "cli only";
	exit;
}

$tempEnvironmentCommand = "sudo /opt/pcsensor-temper/pcsensor -s";
$tempEnvironment = (float)shell_exec($tempEnvironmentCommand);

$tempGpuCommand = "sudo /opt/vc/bin/vcgencmd measure_temp";
$tempGpu = shell_exec($tempGpuCommand);
$tempGpu = str_replace("temp=","",$tempGpu);
$tempGpu = (float)str_replace("'C","",$tempGpu);

$tempCpuCommand = "cat /sys/class/thermal/thermal_zone0/temp";
$tempCpu = shell_exec($tempCpuCommand);
$tempCpu = (float)$tempCpu/1000;

require_once('db.php');

try{
	$sql = "INSERT INTO temperature (environment,cpu,gpu,scale) VALUES (:environment,:cpu,:gpu,'C')";
	$stmt = $pdo->prepare($sql);

	$data = [
		'environment' => $tempEnvironment,
		'cpu'         => $tempCpu,
		'gpu'         => $tempGpu
	];

	//var_dump($data);

	$stmt->execute($data);

	$pdo = null;
} catch (PDOException $e){
	echo $e->getMessage() . "\n";
	echo $e->getCode() . "\n";

	$pdo->rollback();
}
