<?php
/* patch_geo_columns.php
 * Añade columnas lat y lng a usuarios si aún no existen.
 * Abrir en el navegador una sola vez y luego eliminar.
 */
$pdo = new PDO("sqlite:chat.db");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$cols = $pdo->query("PRAGMA table_info(usuarios)")->fetchAll(PDO::FETCH_COLUMN, 1);

if (!in_array('lat', $cols)) {
    $pdo->exec("ALTER TABLE usuarios ADD COLUMN lat REAL");
    echo "➕ Columna lat creada.<br>";
}
if (!in_array('lng', $cols)) {
    $pdo->exec("ALTER TABLE usuarios ADD COLUMN lng REAL");
    echo "➕ Columna lng creada.<br>";
}
echo "✅ Todo listo: usuarios(lat,lng) disponible.";

// Descomenta para autodestrucción
// unlink(__FILE__);
?>
