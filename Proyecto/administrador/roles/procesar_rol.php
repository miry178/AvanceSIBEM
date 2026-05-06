<?php
header('Content-Type: application/json');
session_start();
require_once '../../bd/conexion.php';

if (!isset($_SESSION['idUsuario'])) {
    echo json_encode(['ok' => false, 'error' => 'No autorizado']);
    exit;
}

$esAdmin = ($_SESSION['tipoUsuario'] ?? '') === 'Administrador';

// ── ELIMINAR ROL ───────────────────────────────────────────────
if (!empty($_POST['eliminar'])) {
    if (!$esAdmin) {
        echo json_encode(['ok' => false, 'error' => 'Solo el Administrador puede eliminar roles']);
        exit;
    }
    $idRol = intval($_POST['idRol'] ?? 0);
    if (!$idRol) { echo json_encode(['ok' => false, 'error' => 'ID inválido']); exit; }
    if ($idRol === 1) { echo json_encode(['ok' => false, 'error' => 'El rol Administrador no puede eliminarse']); exit; }
    try {
        $pdo->beginTransaction();
        $pdo->prepare("DELETE FROM RolPermiso WHERE idRol = ?")->execute([$idRol]);
        $pdo->prepare("UPDATE Usuario SET idRol = NULL WHERE idRol = ?")->execute([$idRol]);
        $pdo->prepare("DELETE FROM RelRol WHERE idRol = ?")->execute([$idRol]);
        $pdo->prepare("DELETE FROM Rol WHERE idRol = ?")->execute([$idRol]);
        $pdo->commit();
        echo json_encode(['ok' => true, 'mensaje' => 'Rol eliminado correctamente']);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

// ── CREAR / EDITAR ROL ─────────────────────────────────────────
if (!empty($_POST['guardarRol'])) {
    $nombre = trim($_POST['nombre']      ?? '');
    $desc   = trim($_POST['descripcion'] ?? '');
    $idRol  = intval($_POST['idRol']     ?? 0);
    if (!$nombre) { echo json_encode(['ok' => false, 'error' => 'El nombre es obligatorio']); exit; }
    try {
        if ($idRol) {
            $pdo->prepare("UPDATE Rol SET descripcion = ? WHERE idRol = ?")->execute([$nombre, $idRol]);
            echo json_encode(['ok' => true, 'mensaje' => 'Rol actualizado correctamente']);
        } else {
            $pdo->prepare("INSERT INTO Rol (descripcion) VALUES (?)")->execute([$nombre]);
            echo json_encode(['ok' => true, 'mensaje' => 'Rol creado correctamente']);
        }
    } catch (Exception $e) {
        echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

// ── GUARDAR PERMISOS ───────────────────────────────────────────
$idRol        = intval($_POST['idRol']   ?? 0);
$permisosJson = $_POST['permisos']       ?? '[]';
$permisos     = json_decode($permisosJson, true) ?? [];

if (!$idRol) { echo json_encode(['ok' => false, 'error' => 'ID de rol inválido']); exit; }

try {
    $pdo->beginTransaction();
    $pdo->prepare("DELETE FROM RolPermiso WHERE idRol = ?")->execute([$idRol]);
    if (!empty($permisos)) {
        $stmt = $pdo->prepare("INSERT INTO RolPermiso (idRol, idPermiso) VALUES (?, ?)");
        foreach ($permisos as $idPermiso) {
            $stmt->execute([$idRol, intval($idPermiso)]);
        }
    }
    $pdo->commit();
    echo json_encode(['ok' => true, 'mensaje' => 'Permisos guardados correctamente']);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}