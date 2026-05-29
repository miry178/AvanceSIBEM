<?php
$pdo = new PDO("mysql:host=localhost;dbname=biblioteca;charset=utf8mb4", "root", "5775", [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

header('Content-Type: application/json');

$idMaterial = intval($_GET['id'] ?? 0);
if (!$idMaterial) {
    echo json_encode(['ok' => false, 'error' => 'ID inválido']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM vista_material WHERE idMaterial = ?");
    $stmt->execute([$idMaterial]);
    $m = $stmt->fetch();
    if ($m) {
        echo json_encode(['ok' => true, 'material' => $m]);
    } else {
        echo json_encode(['ok' => false, 'error' => 'No encontrado']);
    }
} catch (Exception $e) {
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}