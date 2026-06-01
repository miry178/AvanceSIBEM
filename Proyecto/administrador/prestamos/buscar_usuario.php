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
            r.descripcion AS tipoPersona,
            COALESCE(rp.diasPrestamo, 2) AS diasPrestamo
        FROM Usuario u
        JOIN RelRol rr ON u.idUsuario = rr.idUsuario
        JOIN Rol r ON rr.idRol = r.idRol
        LEFT JOIN ReglasPrestamo rp ON rr.idRol = rp.idRol
        WHERE u.idUsuario = ?
        ORDER BY rp.diasPrestamo DESC
        LIMIT 1
    ");
    $stmt->execute([$id]);
    $row = $stmt->fetch();

    if ($row) {
        // Verificar multas pendientes
        $stmtMulta = $pdo->prepare("
            SELECT COUNT(*) AS total
            FROM Multa mu
            JOIN Prestamo p ON mu.idPrestamo = p.idPrestamo
            WHERE p.idUsuario = ? AND mu.pagada = 'no'
        ");
        $stmtMulta->execute([$id]);
        $tieneMulta = $stmtMulta->fetch()['total'] > 0;

        // Verificar préstamos activos vs máximo permitido
        $stmtMax = $pdo->prepare("
            SELECT COUNT(*) AS total FROM Prestamo 
            WHERE idUsuario = ? AND estado IN ('activo', 'vencido')
        ");
        $stmtMax->execute([$id]);
        $totalActivos = $stmtMax->fetch()['total'];

        $stmtRegla = $pdo->prepare("
            SELECT rp.maxPrestamo 
            FROM ReglasPrestamo rp
            JOIN RelRol rr ON rp.idRol = rr.idRol
            WHERE rr.idUsuario = ?
            LIMIT 1
        ");
        $stmtRegla->execute([$id]);
        $regla = $stmtRegla->fetch();
        $maxPrestamo = $regla['maxPrestamo'] ?? 2;
        $excedeMax = $totalActivos >= $maxPrestamo;

        echo json_encode([
            'encontrado'   => true,
            'nombre'       => $row['nombre'],
            'correo'       => $row['correoInst'],
            'tipoPersona'  => $row['tipoPersona'] ?? 'Sin tipo',
            'diasPrestamo' => $row['diasPrestamo'] ?? 0,
            'tieneMulta'   => $tieneMulta,
            'excedeMax'    => $excedeMax,
            'maxPrestamo'  => $maxPrestamo
        ]);
    } else {
        echo json_encode(['encontrado' => false]);
    }
} catch (Exception $e) {
    echo json_encode(['encontrado' => false, 'error' => $e->getMessage()]);
}