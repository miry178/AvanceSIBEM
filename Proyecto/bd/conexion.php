<?php
// Forzar charset compatible antes de conectar
if (!defined('MYSQLI_OPT_CHARSET_NAME')) {
    define('MYSQLI_OPT_CHARSET_NAME', 7);
}

$host     = "localhost";
$usuario  = "root";
$password = "5775";
$bd       = "biblioteca";

//$host     = "localhost";
//$usuario  = "u310586406_sibem";
//$password = "Miry18nov";
//$bd       = "u310586406_biblioteca";

$conn = new mysqli($host, $usuario, $password, $bd);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
$conn->set_charset("utf8");

// ── Función para verificar permisos ──────────────────────────
function tienePermiso($conn, $idUsuario, $modulo, $accion) {
    $stmt = $conn->prepare("
        SELECT COUNT(*) 
        FROM RolPermiso rp
        JOIN RelRol rr ON rp.idRol = rr.idRol
        JOIN Permiso p ON rp.idPermiso = p.idPermiso
        WHERE rr.idUsuario = ?
        AND p.modulo = ?
        AND p.accion = ?
    ");
    $stmt->bind_param("sss", $idUsuario, $modulo, $accion);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $fila      = $resultado->fetch_row();
    return $fila[0] > 0;
}
?>