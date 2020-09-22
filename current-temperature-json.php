<?php

require_once('db.php');

header('Content-Type: application/json');

$sql = "SELECT environment as last, MAX(environment) as max, MIN(environment) as min FROM temperature WHERE scale = 'C' ORDER BY id DESC LIMIT 1";

$stmt = $pdo->query($sql);

$temperatures = $stmt->fetchAll();

$last = (float)$temperatures[0]['last'];
$max  = (float)$temperatures[0]['max'];
$min  = (float)$temperatures[0]['min'];

$data[] = array('last' => $last,'max' => $max,'min' => $min);

echo json_encode($data);

$pdo = null;

