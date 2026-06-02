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

    $conn->begin_transaction();
    try {
        $s1 = $conn->prepare("DELETE FROM RolPermiso WHERE idRol = ?");
        $s1->bind_param("i", $idRol);
        $s1->execute();

        $s2 = $conn->prepare("UPDATE Usuario SET idRol = NULL WHERE idRol = ?");
        $s2->bind_param("i", $idRol);
        $s2->execute();

        $s3 = $conn->prepare("DELETE FROM RelRol WHERE idRol = ?");
        $s3->bind_param("i", $idRol);
        $s3->execute();

        $s4 = $conn->prepare("DELETE FROM Rol WHERE idRol = ?");
        $s4->bind_param("i", $idRol);
        $s4->execute();

        $conn->commit();
        echo json_encode(['ok' => true, 'mensaje' => 'Rol eliminado correctamente']);
    } catch (Exception $e) {
        $conn->rollback();
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

    if ($idRol) {
        $stmt = $conn->prepare("UPDATE Rol SET descripcion = ? WHERE idRol = ?");
        $stmt->bind_param("si", $nombre, $idRol);
        $stmt->execute();
        echo json_encode(['ok' => true, 'mensaje' => 'Rol actualizado correctamente']);
    } else {
        $stmt = $conn->prepare("INSERT INTO Rol (descripcion) VALUES (?)");
        $stmt->bind_param("s", $nombre);
        $stmt->execute();
        echo json_encode(['ok' => true, 'mensaje' => 'Rol creado correctamente']);
    }
    exit;
}

// ── GUARDAR PERMISOS ───────────────────────────────────────────
$idRol        = intval($_POST['idRol']   ?? 0);
$permisosJson = $_POST['permisos']       ?? '[]';
$permisos     = json_decode($permisosJson, true) ?? [];

if (!$idRol) { echo json_encode(['ok' => false, 'error' => 'ID de rol inválido']); exit; }

$conn->begin_transaction();
try {
    // Borrar permisos actuales del rol
    $s1 = $conn->prepare("DELETE FROM RolPermiso WHERE idRol = ?");
    $s1->bind_param("i", $idRol);
    $s1->execute();

    // Insertar nuevos permisos
    if (!empty($permisos)) {
        $stmt = $conn->prepare("INSERT INTO RolPermiso (idRol, idPermiso) VALUES (?, ?)");
        foreach ($permisos as $idPermiso) {
            $idPermiso = intval($idPermiso);
            $stmt->bind_param("ii", $idRol, $idPermiso);
            $stmt->execute();
        }
    }

    $conn->commit();
    echo json_encode(['ok' => true, 'mensaje' => 'Permisos guardados correctamente']);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}
?>