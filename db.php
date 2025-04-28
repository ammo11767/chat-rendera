<?php
/* db.php  - conexión PDO + autoparche lat/lng */
function getPDO() {
    static $pdo = null;
    if ($pdo) return $pdo;

    $pdo = new PDO("sqlite:" . __DIR__ . "/chat.db");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // autoparche: añade lat/lng si no existen
    $cols = $pdo->query("PRAGMA table_info(usuarios)")->fetchAll(PDO::FETCH_COLUMN, 1);
    $pdo->beginTransaction();
    if (!in_array('lat', $cols))  $pdo->exec("ALTER TABLE usuarios ADD COLUMN lat REAL");
    if (!in_array('lng', $cols))  $pdo->exec("ALTER TABLE usuarios ADD COLUMN lng REAL");
    $pdo->commit();

    return $pdo;
}
?>