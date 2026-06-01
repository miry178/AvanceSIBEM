<?php
$conn = new mysqli("localhost", "root", "5775", "biblioteca");
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Verificar que llegaron los campos obligatorios
$idUsuario      = trim($_POST['idUsuario']      ?? '');
$idEjemplar     = trim($_POST['idEjemplar']     ?? '');
$fechaPrestamo  = trim($_POST['fechaPrestamo']  ?? '');
$fechaDevolucion= trim($_POST['fechaDevolucion']?? '');

if (!$idUsuario || !$idEjemplar || !$fechaPrestamo || !$fechaDevolucion) {
    header("Location: prestamos.php?error=campos_vacios");
    exit;
}

// Verificar que el ejemplar sigue disponible
$check = $conn->prepare("SELECT estado FROM Ejemplar WHERE idEjemplar = ?");
$check->bind_param("i", $idEjemplar);
$check->execute();
$ejemplar = $check->get_result()->fetch_assoc();

if (!$ejemplar || $ejemplar['estado'] !== 'disponible') {
    header("Location: prestamos.php?error=no_disponible");
    exit;
}

// Verificar que el usuario no exceda el máximo de préstamos permitidos
$checkMax = $conn->prepare("
    SELECT COUNT(*) AS total FROM Prestamo 
    WHERE idUsuario = ? AND estado IN ('activo', 'vencido')
");
$checkMax->bind_param("s", $idUsuario);
$checkMax->execute();
$totalPrestamos = $checkMax->get_result()->fetch_assoc()['total'];

// Obtener el máximo permitido según el rol del usuario
$checkRegla = $conn->prepare("
    SELECT rp.maxPrestamo 
    FROM ReglasPrestamo rp
    JOIN RelRol rr ON rp.idRol = rr.idRol
    WHERE rr.idUsuario = ?
    LIMIT 1
");
$checkRegla->bind_param("s", $idUsuario);
$checkRegla->execute();
$regla = $checkRegla->get_result()->fetch_assoc();
$maxPrestamo = $regla['maxPrestamo'] ?? 2;

if ($totalPrestamos >= $maxPrestamo) {
    header("Location: prestamos.php?error=max_prestamos");
    exit;
}

// Obtener correo del usuario
$uq = $conn->prepare("SELECT correoInst FROM Usuario WHERE idUsuario = ?");
$uq->bind_param("s", $idUsuario);
$uq->execute();
$usuario = $uq->get_result()->fetch_assoc();
$correoInst = $usuario['correoInst'] ?? '';

// Verificar que el usuario no tenga multas pendientes
$checkMulta = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM Multa mu
    JOIN Prestamo p ON mu.idPrestamo = p.idPrestamo
    WHERE p.idUsuario = ? AND mu.pagada = 'no'
");
$checkMulta->bind_param("s", $idUsuario);
$checkMulta->execute();
$tieneMulta = $checkMulta->get_result()->fetch_assoc()['total'];

if ($tieneMulta > 0) {
    header("Location: prestamos.php?error=tiene_adeudo");
    exit;
}


// Insertar el préstamo
$stmt = $conn->prepare("
    INSERT INTO Prestamo (idUsuario, correoInst, idEjemplar, fechaPrestamo, fechaDevolucion, estado)
    VALUES (?, ?, ?, ?, ?, 'activo')
");
$stmt->bind_param("ssiss", $idUsuario, $correoInst, $idEjemplar, $fechaPrestamo, $fechaDevolucion);

if (!$stmt->execute()) {
    header("Location: prestamos.php?error=insert_fallido");
    exit;
}

// Marcar el ejemplar como prestado
$upd = $conn->prepare("UPDATE Ejemplar SET estado = 'prestado' WHERE idEjemplar = ?");
$upd->bind_param("i", $idEjemplar);
$upd->execute();

$conn->close();
header("Location: prestamos.php?exito=1");
exit;