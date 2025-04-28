<?php
$pdo = new PDO("sqlite:chat.db");
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    if (!empty($nombre)) {
        $stmt = $pdo->prepare("INSERT OR IGNORE INTO usuarios (nombre) VALUES (?)");
        $stmt->execute([$nombre]);
        header("Location: contactos.php?yo=" . urlencode($nombre));
        exit;
    } else {
        $error = "El nombre no puede estar vacío";
    }
}
?>
<h2>Registro de Usuario</h2>
<?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
<form method="POST">
  <input name="nombre" required placeholder="Tu nombre">
  <button type="submit">Entrar</button>
</form>