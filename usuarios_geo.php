<?php
require 'db.php';
$pdo = getPDO();
header('Content-Type: application/json');
$data = $pdo->query("SELECT nombre, lat, lng FROM usuarios WHERE lat IS NOT NULL AND lng IS NOT NULL")->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($data);
?>