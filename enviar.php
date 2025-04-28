<?php
$pdo = new PDO("sqlite:chat.db");
$de = $_POST['de'];
$para = $_POST['para'];
$mensaje = $_POST['mensaje'];
$stmt = $pdo->prepare("INSERT INTO mensajes (emisor, receptor, mensaje) VALUES (?, ?, ?)");
$stmt->execute([$de, $para, $mensaje]);
?>