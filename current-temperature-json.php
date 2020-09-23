<?php

require_once('db.php');

header('Content-Type: application/json');

$sql = "SELECT environment as last, (select MAX(environment) from temperature) as max, (select MIN(environment) from temperature) as min FROM temperature WHERE scale = 'C' AND timestamp = (select max(timestamp) from temperature)";

$stmt = $pdo->query($sql);

$temperatures = $stmt->fetchAll();

$last = (float)$temperatures[0]['last'];
$max  = (float)$temperatures[0]['max'];
$min  = (float)$temperatures[0]['min'];

$data[] = array('last' => $last,'max' => $max,'min' => $min);

echo json_encode($data);

$pdo = null;

