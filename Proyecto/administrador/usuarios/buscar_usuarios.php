<?php
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "5775", "biblioteca");
if ($conn->connect_error) {
    echo json_encode(['ok' => false, 'error' => 'Error de conexión']);
    exit;
}

$pdo = new PDO("mysql:host=localhost;dbname=biblioteca;charset=utf8mb4", "root", "5775", [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

try {
    $stmt = $pdo->query("SELECT * FROM vista_usuarios ORDER BY nombre ASC");
    $usuarios = $stmt->fetchAll();

    $resultado = array_map(function($u) {
        return [
            'idUsuario'     => $u['idUsuario'],
            'nombre'        => $u['nombre'],
            'correoInst'    => $u['correoInst'],
            'activo'        => $u['activo'],
            'estadoLabel'   => $u['activo'] === 'si' ? 'Activo' : 'Inactivo',
            'idTipoPersona' => $u['idRol'],
            'tipoPersona'   => $u['tipoPersona'] ?? 'Sin tipo',
            'idCarrera'     => $u['idCarrera'],
            'idDivision'    => $u['idDivision'],
            'extra'         => $u['carrera'] ?? $u['division'] ?? '—',
        ];
    }, $usuarios);

    echo json_encode(['ok' => true, 'usuarios' => $resultado]);

} catch (Exception $e) {
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}