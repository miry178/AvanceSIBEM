<?php
header('Content-Type: application/json');
require_once '../../bd/conexion.php';

$id = $_GET['id'] ?? '';
if ($id === '') { echo json_encode(['encontrado' => false]); exit; }

try {
    $stmt = $pdo->prepare("
        SELECT 
            u.nombre,
            u.correoInst,
            r.descripcion   AS tipoPersona,
            COALESCE(rp.diasPrestamo, 2) AS diasPrestamo
        FROM Usuario u
        JOIN RelRol rr          ON u.idUsuario = rr.idUsuario
        JOIN Rol r              ON rr.idRol    = r.idRol
        LEFT JOIN ReglasPrestamo rp ON rr.idRol = rp.idRol
        WHERE u.idUsuario = ?
        ORDER BY rp.diasPrestamo DESC
        LIMIT 1
    ");
    $stmt->execute([$id]);
    $row = $stmt->fetch();

    if ($row) {
        echo json_encode([
            'encontrado'   => true,
            'nombre'       => $row['nombre'],
            'correo'       => $row['correoInst'],
            'tipoPersona'  => $row['tipoPersona']  ?? 'Sin tipo',
            'diasPrestamo' => $row['diasPrestamo'] ?? 0
        ]);
    } else {
        echo json_encode(['encontrado' => false]);
    }
} catch (Exception $e) {
    echo json_encode(['encontrado' => false, 'error' => $e->getMessage()]);
}