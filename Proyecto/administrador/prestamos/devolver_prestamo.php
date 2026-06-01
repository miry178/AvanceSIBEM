<?php
header('Content-Type: application/json');
session_start();
require_once '../../bd/conexion.php';

if (!isset($_SESSION['idUsuario'])) {
    echo json_encode(['ok' => false, 'error' => 'No autorizado']);
    exit;
}

$idPrestamo = intval($_POST['idPrestamo'] ?? 0);

if (!$idPrestamo) {
    echo json_encode(['ok' => false, 'error' => 'ID inválido']);
    exit;
}

try {
    // Obtener datos del préstamo
    $stmt = $pdo->prepare("
        SELECT p.idEjemplar, p.fechaDevolucion, p.estado,
               rp.precioMulta
        FROM Prestamo p
        JOIN Usuario u ON p.idUsuario = u.idUsuario
        JOIN RelRol rr ON u.idUsuario = rr.idUsuario
        JOIN ReglasPrestamo rp ON rr.idRol = rp.idRol
        WHERE p.idPrestamo = ?
        LIMIT 1
    ");
    $stmt->execute([$idPrestamo]);
    $prestamo = $stmt->fetch();

    if (!$prestamo) {
        echo json_encode(['ok' => false, 'error' => 'Préstamo no encontrado']);
        exit;
    }

    $pdo->beginTransaction();

    // Marcar préstamo como devuelto
    $pdo->prepare("UPDATE Prestamo SET estado = 'devuelto' WHERE idPrestamo = ?")
        ->execute([$idPrestamo]);

    // Marcar ejemplar como disponible
    $pdo->prepare("UPDATE Ejemplar SET estado = 'disponible' WHERE idEjemplar = ?")
        ->execute([$prestamo['idEjemplar']]);

    // Si estaba vencido — generar multa
// Si estaba vencido — generar multa solo si no existe ya
    if ($prestamo['estado'] === 'vencido') {
        $fechaDevolucion = new DateTime($prestamo['fechaDevolucion']);
        $hoy             = new DateTime();
        $diasRetraso     = $hoy->diff($fechaDevolucion)->days;
        $precioMulta     = $prestamo['precioMulta'] ?? 25.00;
        $monto           = $diasRetraso * $precioMulta;

        if ($monto > 0) {
            // Verificar si ya existe multa para este préstamo
            $existeMulta = $pdo->prepare("SELECT idMulta FROM Multa WHERE idPrestamo = ?");
            $existeMulta->execute([$idPrestamo]);
            
            if (!$existeMulta->fetch()) {
                $pdo->prepare("
                    INSERT INTO Multa (idPrestamo, monto, pagada)
                    VALUES (?, ?, 'no')
                ")->execute([$idPrestamo, $monto]);
            }
        }
    }

    $pdo->commit();
    echo json_encode(['ok' => true, 'mensaje' => 'Préstamo registrado como devuelto correctamente']);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}