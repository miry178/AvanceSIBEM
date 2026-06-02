<?php
session_start();
require_once '../../bd/conexion.php';

// Verificar que el usuario esté logueado
if (!isset($_SESSION['idUsuario'])) {
    header("Location: ../../index.php?error=2");
    exit();
}

$idMulta = intval($_POST['idMulta'] ?? 0);

if (!$idMulta) {
    header("Location: adeudos.php?error=1");
    exit;
}

// Actualizar multa como pagada con la fecha de hoy
$stmt = $conn->prepare("
    UPDATE Multa 
    SET pagada = 'si', fechaPago = NOW() 
    WHERE idMulta = ? AND pagada = 'no'
");
$stmt->bind_param("i", $idMulta);
$stmt->execute();

// affected_rows indica cuántas filas se modificaron
// si es mayor a 0 significa que sí se actualizó
if ($stmt->affected_rows > 0) {
    header("Location: adeudos.php?exito=1");
} else {
    header("Location: adeudos.php?error=1");
}
exit();
?>