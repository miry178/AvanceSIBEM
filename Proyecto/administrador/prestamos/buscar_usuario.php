<?php
header('Content-Type: application/json');
require_once '../../bd/conexion.php';

$id = $_GET['id'] ?? '';
if ($id === '') { echo json_encode(['encontrado' => false]); exit; }

// Buscar usuario
$stmt = $conn->prepare("
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
$stmt->bind_param("s", $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

if ($row) {
    // Verificar multas pendientes
    $stmtMulta = $conn->prepare("
        SELECT COUNT(*) AS total
        FROM Multa mu
        JOIN Prestamo p ON mu.idPrestamo = p.idPrestamo
        WHERE p.idUsuario = ? AND mu.pagada = 'no'
    ");
    $stmtMulta->bind_param("s", $id);
    $stmtMulta->execute();
    $tieneMulta = $stmtMulta->get_result()->fetch_assoc()['total'] > 0;

        // Verificar si tiene préstamos vencidos
    $stmtVencidos = $conn->prepare("
        SELECT COUNT(*) AS total FROM Prestamo 
        WHERE idUsuario = ? AND estado = 'vencido'
    ");
    $stmtVencidos->bind_param("s", $id);
    $stmtVencidos->execute();
    $tieneVencidos = $stmtVencidos->get_result()->fetch_assoc()['total'] > 0;


    // Verificar préstamos activos vs máximo permitido
    $stmtMax = $conn->prepare("
        SELECT COUNT(*) AS total FROM Prestamo 
        WHERE idUsuario = ? AND estado IN ('activo', 'vencido')
    ");
    $stmtMax->bind_param("s", $id);
    $stmtMax->execute();
    $totalActivos = $stmtMax->get_result()->fetch_assoc()['total'];

    // Obtener máximo permitido
    $stmtRegla = $conn->prepare("
        SELECT rp.maxPrestamo 
        FROM ReglasPrestamo rp
        JOIN RelRol rr ON rp.idRol = rr.idRol
        WHERE rr.idUsuario = ?
        LIMIT 1
    ");
    $stmtRegla->bind_param("s", $id);
    $stmtRegla->execute();
    $regla       = $stmtRegla->get_result()->fetch_assoc();
    $maxPrestamo = $regla['maxPrestamo'] ?? 2;
    $excedeMax   = $totalActivos >= $maxPrestamo;

    // Verificar si es Docente para darle más días
$stmtDocente = $conn->prepare("SELECT idUsuario FROM Docente WHERE idUsuario = ?");
$stmtDocente->bind_param("s", $id);
$stmtDocente->execute();
$esDocente = $stmtDocente->get_result()->num_rows > 0;

// Asignar días según tipo de persona
$diasPrestamo = $esDocente ? 5 : ($row['diasPrestamo'] ?? 2);

echo json_encode([
    'encontrado'   => true,
    'nombre'       => $row['nombre'],
    'correo'       => $row['correoInst'],
    'tipoPersona'  => $row['tipoPersona'] ?? 'Sin tipo',
    'diasPrestamo' => $diasPrestamo,
    'tieneMulta'   => $tieneMulta,
    'tieneVencidos'=> $tieneVencidos,
    'excedeMax'    => $excedeMax,
    'maxPrestamo'  => $maxPrestamo
]);
} else {
    echo json_encode(['encontrado' => false]);
}
?>