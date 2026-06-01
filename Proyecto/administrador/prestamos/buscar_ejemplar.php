<?php
// Conexión - ajusta con tus datos
$conn = new mysqli("localhost", "root", "5775", "biblioteca");

// Recibe el código que mandó JavaScript
$codigo = $_GET['codigo'] ?? '';

if ($codigo === '') {
    echo json_encode(['error' => 'Código vacío']);
    exit;
}

// Busca el ejemplar y el título del material al que pertenece
$stmt = $conn->prepare("
    SELECT e.idEjemplar, e.codigoEjemplar, e.estado, m.titulo, m.autor
    FROM Ejemplar e
    JOIN Material m ON e.idMaterial = m.idMaterial
    WHERE e.codigoEjemplar = ?
");
    
$stmt->bind_param("s", $codigo);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    $ejemplar = $resultado->fetch_assoc();

    // Si ya está prestado, avisamos
    if ($ejemplar['estado'] === 'prestado') {
        echo json_encode([
            'encontrado' => true,
            'disponible' => false,
            'titulo'     => $ejemplar['titulo']
        ]);
    } else {
        echo json_encode([
            'encontrado' => true,
            'disponible' => true,
            'idEjemplar' => $ejemplar['idEjemplar'],
            'titulo'     => $ejemplar['titulo'],
            'autor'      => $ejemplar['autor']
        ]);
    }
} else {
    echo json_encode(['encontrado' => false]);
}

$conn->close();
?>