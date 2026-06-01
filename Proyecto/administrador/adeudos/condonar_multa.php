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
    exit();
}

try {
    $pdo->prepare("DELETE FROM Multa WHERE idMulta = ?")
        ->execute([$idMulta]);
    header("Location: adeudos.php?condonada=1");
} catch (Exception $e) {
    header("Location: adeudos.php?error=1");
}
exit();