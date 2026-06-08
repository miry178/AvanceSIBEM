<?php
require_once '../../bd/conexion.php';
header('Content-Type: application/json');

$isbn = trim($_GET['isbn'] ?? '');

if ($isbn === '') {
    echo json_encode(['error' => 'ISBN vacío']);
    exit;
}

// Buscar el material por ISBN y un ejemplar disponible
$stmt = $conn->prepare("
    SELECT e.idEjemplar,e.codigoEjemplar,
        e.estado,m.titulo,m.autor,m.isbn,
        COALESCE(ed.nombre, m.editorial) AS editorial
    FROM Material m
    LEFT JOIN Editorial ed ON m.idEditorial = ed.idEditorial
    JOIN Ejemplar e ON e.idMaterial = m.idMaterial
    WHERE m.isbn = ?
    ORDER BY FIELD(e.estado, 'disponible', 'prestado', 'baja')
    LIMIT 1
");
$stmt->bind_param("s", $isbn);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

if (!$row) {
    echo json_encode(['encontrado' => false]);
    exit;
}

if ($row['estado'] !== 'disponible') {
    echo json_encode([
        'encontrado' => true,
        'disponible' => false,
        'titulo'     => $row['titulo']
    ]);
    exit;
}

echo json_encode([
    'encontrado' => true,
    'disponible' => true,
    'idEjemplar' => $row['idEjemplar'],
    'titulo'     => $row['titulo'],
    'autor'      => $row['autor'],
    'isbn'       => $row['isbn'],
    'editorial'  => $row['editorial']
]);

$conn->close();