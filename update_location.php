<?php
require 'db.php';
$pdo = getPDO();
$nombre = $_POST['nombre'] ?? '';
$lat = $_POST['lat'] ?? null;
$lng = $_POST['lng'] ?? null;
if ($nombre && $lat && $lng) {
  $stmt = $pdo->prepare("UPDATE usuarios SET lat = ?, lng = ? WHERE nombre = ?");
  $stmt->execute([$lat, $lng, $nombre]);
}
?>