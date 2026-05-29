<?php
$conn = new mysqli("localhost", "root", "5775", "biblioteca");
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$pdo = new PDO("mysql:host=localhost;dbname=biblioteca;charset=utf8mb4", "root", "5775", [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
]);

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

if ($q !== '') {
    switch ($campo) {
        case 'titulo':
            $where[]  = 'titulo LIKE ?';
            $params[] = "%$q%";
            break;
        case 'autor':
            $where[]  = 'autor LIKE ?';
            $params[] = "%$q%";
            break;
        case 'isbn':
            $where[]  = 'isbn LIKE ?';
            $params[] = "%$q%";
            break;
        case 'editorial':
            $where[]  = 'editorial LIKE ?';
            $params[] = "%$q%";
            break;
        default:
            $where[]  = '(titulo LIKE ? OR autor LIKE ? OR isbn LIKE ? OR editorial LIKE ?)';
            $params[] = "%$q%";
            $params[] = "%$q%";
            $params[] = "%$q%";
            $params[] = "%$q%";
    }
}

if ($clasificacion !== '') {
    $where[]  = 'clasificacion = ?';
    $params[] = $clasificacion;
}

if ($tipo !== '') {
    $where[]  = 'tipoMaterial = ?';
    $params[] = $tipo;
}

$orderMap = [
    'titulo'      => 'titulo ASC',
    'disponibles' => 'disponibles DESC',
    'ejemplares'  => 'totalEjemplares DESC',
    'autor'       => 'autor ASC',
];
$orderSQL = $orderMap[$orden] ?? 'titulo ASC';

$having = '';
if ($estado === 'disponible')   $having = 'HAVING disponibles > 0';
if ($estado === 'nodisponible') $having = 'HAVING disponibles = 0';

$whereSQL = implode(' AND ', $where);

// Contar total de resultados
$sqlTotal = "SELECT COUNT(*) FROM (
    SELECT idMaterial FROM vista_material
    WHERE $whereSQL
    $having
) AS total";

$stmtTotal = $pdo->prepare($sqlTotal);
$stmtTotal->execute($params);
$total = (int)$stmtTotal->fetchColumn();
$totalPaginas = ceil($total / $porPagina);

// Consulta con paginación
$sql = "SELECT * FROM vista_material
        WHERE $whereSQL
        $having
        ORDER BY $orderSQL
        LIMIT $porPagina OFFSET $offset";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $materiales = $stmt->fetchAll();

    echo json_encode([
        'ok'           => true,
        'total'        => $total,
        'pagina'       => $pagina,
        'totalPaginas' => $totalPaginas,
        'porPagina'    => $porPagina,
        'materiales'   => $materiales,
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}