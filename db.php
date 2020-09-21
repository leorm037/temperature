<?php

$host = "127.0.0.1";
$db   = "temperature";
$user = "temperature";
$pass = "temperature";

$dsn = "mysql:host=$host;dbname=$db";

$options = [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION];

try{
        $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e){
        echo $e->getMessage() . "\n";
        echo $e->getCode() . "\n";
}
