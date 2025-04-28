<?php
header("Content-Type: application/json");
$pdo = new PDO("sqlite:chat.db");
$yo   = $_GET['yo']   ?? '';
$peer = $_GET['peer'] ?? '';

$stmt = $pdo->prepare(
    "SELECT * FROM mensajes
     WHERE (emisor = ? AND receptor = ?)
        OR (emisor = ? AND receptor = ?)
     ORDER BY fecha ASC"
);
$stmt->execute([$yo, $peer, $peer, $yo]);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>