<?php
session_start();
require_once '../../bd/conexion.php';

if (!isset($_SESSION['idUsuario'])) {
    echo json_encode(['ok' => false, 'error' => 'No autorizado']);
    exit;
}

header('Content-Type: application/json');

$idMaterial = intval($_POST['idMaterial'] ?? 0);

if (!$idMaterial) {
    echo json_encode(['ok' => false, 'error' => 'ID inválido']);
    exit;
}

// Verificar que no tenga ejemplares prestados actualmente
$check = $conn->prepare("
    SELECT COUNT(*) AS total 
    FROM Ejemplar e
    JOIN Prestamo p ON e.idEjemplar = p.idEjemplar
    WHERE e.idMaterial = ? AND p.estado IN ('activo', 'vencido')
");
$check->bind_param("i", $idMaterial);
$check->execute();
$tienePrestamos = $check->get_result()->fetch_assoc()['total'];

if ($tienePrestamos > 0) {
    echo json_encode(['ok' => false, 'error' => 'No se puede dar de baja, el material tiene préstamos activos']);
    exit;
}

// Marcar todos los ejemplares como baja
$stmt = $conn->prepare("UPDATE Ejemplar SET estado = 'baja' WHERE idMaterial = ?");
$stmt->bind_param("i", $idMaterial);

if ($stmt->execute()) {
    echo json_encode(['ok' => true, 'mensaje' => 'Material dado de baja correctamente']);
} else {
    echo json_encode(['ok' => false, 'error' => 'Error al dar de baja el material']);
}
?>