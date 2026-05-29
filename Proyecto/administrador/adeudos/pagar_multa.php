<?php
session_start();
require_once '../../bd/conexion.php';

if (!isset($_SESSION['idUsuario'])) {
    header("Location: ../../index.php?error=2");
    exit();
}

$idMulta = intval($_POST['idMulta'] ?? 0);

if (!$idMulta) {
    header("Location: adeudos.php?error=1");
    exit;
}

try {
    $stmt = $pdo->prepare("
        UPDATE Multa 
        SET pagada = 'si', fechaPago = NOW() 
        WHERE idMulta = ? AND pagada = 'no'
    ");
    $stmt->execute([$idMulta]);

    if ($stmt->rowCount() > 0) {
        header("Location: adeudos.php?exito=1");
    } else {
        header("Location: adeudos.php?error=1");
    }
} catch (Exception $e) {
    header("Location: adeudos.php?error=1");
}