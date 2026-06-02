<?php
header('Content-Type: application/json');
session_start();
require_once '../../bd/conexion.php';

// Verificar que el usuario esté logueado
if (!isset($_SESSION['idUsuario'])) {
    echo json_encode(['ok' => false, 'error' => 'No autorizado']);
    exit;
}

$idPrestamo = intval($_POST['idPrestamo'] ?? 0);

if (!$idPrestamo) {
    echo json_encode(['ok' => false, 'error' => 'ID inválido']);
    exit;
}

// Obtener datos del préstamo: ejemplar, fecha de devolución, estado y precio de multa
$stmt = $conn->prepare("
    SELECT p.idEjemplar, p.fechaDevolucion, p.estado,
           rp.precioMulta
    FROM Prestamo p
    JOIN Usuario u ON p.idUsuario = u.idUsuario
    JOIN RelRol rr ON u.idUsuario = rr.idUsuario
    JOIN ReglasPrestamo rp ON rr.idRol = rp.idRol
    WHERE p.idPrestamo = ?
    LIMIT 1
");
$stmt->bind_param("i", $idPrestamo);
$stmt->execute();
$prestamo = $stmt->get_result()->fetch_assoc();

if (!$prestamo) {
    echo json_encode(['ok' => false, 'error' => 'Préstamo no encontrado']);
    exit;
}

// Iniciar transacción para que todo se guarde junto o nada
$conn->begin_transaction();

try {
    // Marcar préstamo como devuelto
    $stmt1 = $conn->prepare("UPDATE Prestamo SET estado = 'devuelto' WHERE idPrestamo = ?");
    $stmt1->bind_param("i", $idPrestamo);
    $stmt1->execute();

    // Marcar ejemplar como disponible nuevamente
    $stmt2 = $conn->prepare("UPDATE Ejemplar SET estado = 'disponible' WHERE idEjemplar = ?");
    $stmt2->bind_param("i", $prestamo['idEjemplar']);
    $stmt2->execute();

    // Si el préstamo estaba vencido, generar multa solo si no existe ya
    if ($prestamo['estado'] === 'vencido') {
        $fechaDevolucion = new DateTime($prestamo['fechaDevolucion']);
        $hoy             = new DateTime();
        $diasRetraso     = $hoy->diff($fechaDevolucion)->days;
        $precioMulta     = $prestamo['precioMulta'] ?? 25.00;
        $monto           = $diasRetraso * $precioMulta;

        if ($monto > 0) {
            // Verificar si ya existe una multa para este préstamo
            $existeMulta = $conn->prepare("SELECT idMulta FROM Multa WHERE idPrestamo = ?");
            $existeMulta->bind_param("i", $idPrestamo);
            $existeMulta->execute();

            if (!$existeMulta->get_result()->fetch_assoc()) {
                // Insertar multa solo si no existía
                $stmt3 = $conn->prepare("INSERT INTO Multa (idPrestamo, monto, pagada) VALUES (?, ?, 'no')");
                $stmt3->bind_param("id", $idPrestamo, $monto);
                $stmt3->execute();
            }
        }
    }

    $conn->commit();
    echo json_encode(['ok' => true, 'mensaje' => 'Préstamo registrado como devuelto correctamente']);

} catch (Exception $e) {
    // Si algo falla, revertir todos los cambios
    $conn->rollback();
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}
?>