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

$stmt = $conn->prepare("DELETE FROM Multa WHERE idMulta = ?");
$stmt->bind_param("i", $idMulta);

if ($stmt->execute()) {
    header("Location: adeudos.php?condonada=1");
} else {
    header("Location: adeudos.php?error=1");
}
exit();
?>