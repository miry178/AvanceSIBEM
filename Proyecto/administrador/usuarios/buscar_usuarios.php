<?php
header('Content-Type: application/json');
require_once '../../bd/conexion.php';

$result   = $conn->query("SELECT * FROM vista_usuarios ORDER BY nombre ASC");
$usuarios = $result->fetch_all(MYSQLI_ASSOC);

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
?>