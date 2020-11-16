<?php

if (PHP_SAPI !== 'cli' || isset($_SERVER['HTTP_USER_AGENT'])) {
	echo "cli only";
	exit;
}

/* Temperatura ambiente do sensor USB */
$tempEnvironmentCommand = "sudo /opt/pcsensor-temper/pcsensor -s";
$tempEnvironment = (float)shell_exec($tempEnvironmentCommand);

/* Temperatura da GPU do Raspberry PI */
$tempGpuCommand = "sudo /opt/vc/bin/vcgencmd measure_temp";
$tempGpu = shell_exec($tempGpuCommand);
$tempGpu = str_replace("temp=","",$tempGpu);
$tempGpu = (float)str_replace("'C","",$tempGpu);

/* Temperatura da CPU do Raspberry Pi */
$tempCpuCommand = "cat /sys/class/thermal/thermal_zone0/temp";
$tempCpu = shell_exec($tempCpuCommand);
$tempCpu = (float)$tempCpu/1000;

/* Temperatura de Brasília segunda API do Clima Tempo */
$tempClimaTempoUrl = "http://apiadvisor.climatempo.com.br/api/v1/weather/locale/8173/current?token=3a13c12183f4ff8f27f0579b3bae28ce";
$tempClimaTempoJson = file_get_contents($tempClimaTempoUrl);
$tempClimaTempoObj = json_decode($tempClimaTempoJson,true);

require_once('db.php');

try{
	$sql = "INSERT INTO temperature (environment,cpu,gpu,scale,external,external_sensation,external_wind_direction,external_wind_velocity,external_humidity,external_condition) VALUES (:environment,:cpu,:gpu,'C',:external,:sensation,:wind_direction,:wind_velocity,:humidity,:condition)";
	$stmt = $pdo->prepare($sql);

	$data = [
		'environment' 		=> $tempEnvironment,
		'cpu'			=> $tempCpu,
		'gpu'			=> $tempGpu,
		'external'		=> $tempClimaTempoObj['data']['temperature'],
		'sensation'	=> $tempClimaTempoObj['data']['sensation'],
		'wind_direction'	=> $tempClimaTempoObj['data']['wind_direction'],
		'wind_velocity'	=> $tempClimaTempoObj['data']['wind_velocity'],
		'humidity'	=> $tempClimaTempoObj['data']['humidity'],
		'condition'	=> $tempClimaTempoObj['data']['condition']
	];

	$stmt->execute($data);

	$pdo = null;
} catch (PDOException $e){
	echo $e->getMessage() . "\n";
	echo $e->getCode() . "\n";

	$pdo->rollback();
}
