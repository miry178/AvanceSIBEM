<?php
require_once '../../bd/conexion.php';
header('Content-Type: application/json');

$idMaterial = intval($_GET['id'] ?? 0);
if (!$idMaterial) {
    echo json_encode(['ok' => false, 'error' => 'ID inválido']);
    exit;
}

$stmt = $conn->prepare("SELECT * FROM vista_material WHERE idMaterial = ?");
$stmt->bind_param("i", $idMaterial);
$stmt->execute();
$m = $stmt->get_result()->fetch_assoc();

if ($m) {
    echo json_encode(['ok' => true, 'material' => $m]);
} else {
    echo json_encode(['ok' => false, 'error' => 'No encontrado']);
}
?>