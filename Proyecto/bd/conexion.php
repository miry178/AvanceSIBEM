<?php
// Forzar charset compatible antes de conectar
if (!defined('MYSQLI_OPT_CHARSET_NAME')) {
    define('MYSQLI_OPT_CHARSET_NAME', 7);
}

$host     = "localhost";
$usuario  = "root";
$password = "5775";
$bd       = "biblioteca";

$conn = new mysqli($host, $usuario, $password, $bd);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
$conn->set_charset("utf8");

$pdo = new PDO("mysql:host=localhost;dbname=biblioteca;charset=utf8;port=3306", "root", "5775", [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => true,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
]);

// ── Función para verificar permisos ──────────────────────────
function tienePermiso($pdo, $idUsuario, $modulo, $accion) {
    try {
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM RolPermiso rp
            JOIN RelRol rr ON rp.idRol = rr.idRol
            JOIN Permiso p ON rp.idPermiso = p.idPermiso
            WHERE rr.idUsuario = ?
            AND p.modulo = ?
            AND p.accion = ?
        ");
        $stmt->execute([$idUsuario, $modulo, $accion]);
        return $stmt->fetchColumn() > 0;
    } catch (Exception $e) {
        return false;
    }
}

?>

