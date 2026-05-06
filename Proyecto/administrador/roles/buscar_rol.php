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

try {
    // Obtener datos del rol
    $stmtRol = $pdo->prepare("SELECT descripcion FROM Rol WHERE idRol = ?");
    $stmtRol->execute([$idRol]);
    $rol = $stmtRol->fetch();

    // Obtener permisos del rol
    $stmt = $pdo->prepare("SELECT idPermiso FROM RolPermiso WHERE idRol = ?");
    $stmt->execute([$idRol]);
    $permisos = array_column($stmt->fetchAll(), 'idPermiso');

    echo json_encode([
        'ok'          => true,
        'descripcion' => $rol['descripcion'] ?? '',
        'permisos'    => $permisos
    ]);
} catch (Exception $e) {
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}