<?php
$yo = $_GET['yo'] ?? '';
if (!$yo) { header("Location: registro.php"); exit; }
$pdo = new PDO("sqlite:chat.db");
$stmt = $pdo->prepare("SELECT nombre FROM usuarios WHERE nombre != ?");
$stmt->execute([$yo]);
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<h2>Hola <?= htmlspecialchars($yo) ?>, elige con quién hablar:</h2>
<ul>
<?php foreach ($usuarios as $u): ?>
  <li>
    <?= htmlspecialchars($u['nombre']) ?> -
    <a href="chat.php?yo=<?= urlencode($yo) ?>&peer=<?= urlencode($u['nombre']) ?>">Ir al chat</a>
  </li>
<?php endforeach; ?>
</ul>