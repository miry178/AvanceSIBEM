<?php
header('Content-Type: application/json');
session_start();
require_once '../../bd/conexion.php';

if (!isset($_SESSION['idUsuario'])) {
    echo json_encode(['ok' => false, 'error' => 'No autorizado']);
    exit;
}

$idRol = intval($_GET['idRol'] ?? 0);
if (!$idRol) {
    echo json_encode(['ok' => false, 'error' => 'ID inválido']);
    exit;
}

// Obtener datos del rol
$stmtRol = $conn->prepare("SELECT descripcion FROM Rol WHERE idRol = ?");
$stmtRol->bind_param("i", $idRol);
$stmtRol->execute();
$rol = $stmtRol->get_result()->fetch_assoc();

// Obtener permisos del rol
$stmt = $conn->prepare("SELECT idPermiso FROM RolPermiso WHERE idRol = ?");
$stmt->bind_param("i", $idRol);
$stmt->execute();
$rows     = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$permisos = array_column($rows, 'idPermiso');

echo json_encode([
    'ok'          => true,
    'descripcion' => $rol['descripcion'] ?? '',
    'permisos'    => $permisos
]);
?>