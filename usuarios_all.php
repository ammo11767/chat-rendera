<?php
require 'db.php';
$pdo = getPDO();
header('Content-Type: application/json');
header('Cache-Control: no-store');
echo json_encode(
  $pdo->query('SELECT nombre, lat, lng FROM usuarios ORDER BY nombre')
      ->fetchAll(PDO::FETCH_ASSOC)
);
?>