<?php
require_once '../../bd/conexion.php';
header('Content-Type: application/json');

$q             = trim($_GET['q']             ?? '');
$campo         = trim($_GET['campo']         ?? 'todo');
$clasificacion = trim($_GET['clasificacion'] ?? '');
$tipo          = trim($_GET['tipo']          ?? '');
$estado        = trim($_GET['estado']        ?? '');
$orden         = trim($_GET['orden']         ?? 'titulo');
$pagina        = max(1, intval($_GET['pagina'] ?? 1));
$porPagina     = 3;
$offset        = ($pagina - 1) * $porPagina;

$where  = ['1=1'];
$params = [];
$types  = '';

if ($q !== '') {
    switch ($campo) {
        case 'titulo':
            $where[]  = 'titulo LIKE ?';
            $params[] = "%$q%";
            $types   .= 's';
            break;
        case 'autor':
            $where[]  = 'autor LIKE ?';
            $params[] = "%$q%";
            $types   .= 's';
            break;
        case 'isbn':
            $where[]  = 'isbn LIKE ?';
            $params[] = "%$q%";
            $types   .= 's';
            break;
        case 'editorial':
            $where[]  = 'editorial LIKE ?';
            $params[] = "%$q%";
            $types   .= 's';
            break;
        default:
            $where[]  = '(titulo LIKE ? OR autor LIKE ? OR isbn LIKE ? OR editorial LIKE ?)';
            $params[] = "%$q%";
            $params[] = "%$q%";
            $params[] = "%$q%";
            $params[] = "%$q%";
            $types   .= 'ssss';
    }
}

if ($clasificacion !== '') {
    $where[]  = 'clasificacion = ?';
    $params[] = $clasificacion;
    $types   .= 's';
}

if ($tipo !== '') {
    $where[]  = 'tipoMaterial = ?';
    $params[] = $tipo;
    $types   .= 's';
}

// Filtro de estado va directo en WHERE
if ($estado === 'disponible')   { $where[] = 'disponibles > 0'; }
if ($estado === 'nodisponible') { $where[] = 'disponibles = 0'; }

$orderMap = [
    'titulo'      => 'titulo ASC',
    'disponibles' => 'disponibles DESC',
    'ejemplares'  => 'totalEjemplares DESC',
    'autor'       => 'autor ASC',
];
$orderSQL = $orderMap[$orden] ?? 'titulo ASC';
$whereSQL = implode(' AND ', $where);

// Contar total de resultados
$sqlTotal = "SELECT COUNT(*) FROM vista_material WHERE $whereSQL";
$stmtTotal = $conn->prepare($sqlTotal);
if ($types && $params) {
    $stmtTotal->bind_param($types, ...$params);
}
$stmtTotal->execute();
$stmtTotal->bind_result($total);
$stmtTotal->fetch();
$stmtTotal->close();
$totalPaginas = ceil($total / $porPagina);

// Consulta con paginación
$sql = "SELECT * FROM vista_material WHERE $whereSQL ORDER BY $orderSQL LIMIT ? OFFSET ?";

$params[] = $porPagina;
$params[] = $offset;
$types   .= 'ii';

$stmt = $conn->prepare($sql);
if ($types && $params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$materiales = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

echo json_encode([
    'ok'           => true,
    'total'        => $total,
    'pagina'       => $pagina,
    'totalPaginas' => $totalPaginas,
    'porPagina'    => $porPagina,
    'materiales'   => $materiales,
]);
?>