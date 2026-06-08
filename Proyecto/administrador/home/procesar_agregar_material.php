<?php
session_start();
require_once '../../bd/conexion.php';
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Campos siempre obligatorios
$titulo          = trim($_POST['titulo']          ?? '');
$autor           = trim($_POST['autor']           ?? '');
$anioPublicacion = trim($_POST['anioPublicacion'] ?? '');
$idEditorial     = trim($_POST['idEditorial']     ?? '') ?: null;
$edicion         = trim($_POST['edicion']         ?? '');
$idTipoMaterial  = trim($_POST['idTipoMaterial']  ?? '');

if (!$titulo || !$autor || !$anioPublicacion || !$idTipoMaterial) {
    header("Location: ../home/inicio.php?error=campos_vacios");
    exit;
}

// Campos opcionales según tipo
$isbn        = trim($_POST['isbn']        ?? '') ?: null;
$idArea      = trim($_POST['idArea']      ?? '') ?: null;
$idCarrera   = trim($_POST['idCarrera']   ?? '') ?: null;
// Si tiene ejemplares prestables, es prestable
$ejemplares = (int)(trim($_POST['ejemplares'] ?? '') ?: trim($_POST['ejemplares_np'] ?? 0));
$esPrestable = ($ejemplares > 0 && isset($_POST['ejemplares'])) ? 'si' : 'no';

// Ejemplares: viene de campoPrestable o campoEjemplaresSolo
$ejemplares  = (int)(trim($_POST['ejemplares']    ?? '') 
            ?: trim($_POST['ejemplares_np']        ?? 0));

if ($ejemplares < 1) {
    header("Location: ../home/inicio.php?error=campos_vacios");
    exit;
}

// Insertar en Material
$stmt = $conn->prepare("
    INSERT INTO Material 
        (titulo, autor, isbn, anioPublicacion, idEditorial, edicion, idTipoMaterial, idArea, idCarrera, esPrestable)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");
$stmt->bind_param(
    "ssssississ",
    $titulo, $autor, $isbn, $anioPublicacion,
    $idEditorial, $edicion, $idTipoMaterial,
    $idArea, $idCarrera, $esPrestable
);

if (!$stmt->execute()) {
    header("Location: ../home/inicio.php?error=insert_fallido");
    exit;
}

$idMaterial = $conn->insert_id;

// Insertar ejemplares
$stmtEj = $conn->prepare("
    INSERT INTO Ejemplar (codigoEjemplar, idMaterial, estado)
    VALUES (?, ?, 'disponible')
");

for ($i = 1; $i <= $ejemplares; $i++) {
    // Código: tipo abreviado + idMaterial + número de ejemplar. Ej: LIB-12-1
    $tiposAbrev = [1 => 'LIB', 2 => 'REV', 3 => 'TES', 4 => 'RES', 5 => 'MUL'];
    $abrev      = $tiposAbrev[$idTipoMaterial] ?? 'MAT';
    $codigo     = $abrev . '-' . $idMaterial . '-' . $i;

    $stmtEj->bind_param("si", $codigo, $idMaterial);
    $stmtEj->execute();
}

header("Location: ../home/inicio.php?exito=1");
exit;