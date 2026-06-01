<?php
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION['idUsuario'])) {
    header("Location: ../../index.php?error=2");
    exit();
}

require_once '../../bd/conexion.php';

$tipo       = $_SESSION['tipoUsuario'] ?? 'Invitado';
$idUsuario  = $_SESSION['idUsuario'];
$esAdmin    = in_array($tipo, ['Administrador', 'Encargado']);
$esPersonal = in_array($_SESSION['tipoPersona'] ?? '', ['Alumno', 'Docente']);
// ── Datos para Admin/Encargado ──────────────────────────────
if ($esAdmin) {

    // Préstamos por mes
    $prestamosPorMes = $conn->query("
        SELECT DATE_FORMAT(fechaPrestamo, '%b %Y') AS mes,
               MONTH(fechaPrestamo) AS numMes,
               YEAR(fechaPrestamo) AS anio,
               COUNT(*) AS total
        FROM Prestamo
        GROUP BY anio, numMes, mes
        ORDER BY anio, numMes
    ")->fetch_all(MYSQLI_ASSOC);

    // Top 5 libros más prestados
    $topLibros = $conn->query("
        SELECT m.titulo, COUNT(*) AS total
        FROM Prestamo p
        JOIN Ejemplar e ON p.idEjemplar = e.idEjemplar
        JOIN Material m ON e.idMaterial = m.idMaterial
        GROUP BY m.idMaterial
        ORDER BY total DESC
        LIMIT 5
    ")->fetch_all(MYSQLI_ASSOC);

    // Usuarios con más préstamos
    $topUsuarios = $conn->query("
        SELECT u.nombre, COUNT(*) AS total
        FROM Prestamo p
        JOIN Usuario u ON p.idUsuario = u.idUsuario
        GROUP BY p.idUsuario
        ORDER BY total DESC
        LIMIT 5
    ")->fetch_all(MYSQLI_ASSOC);

    // Multas recaudadas vs pendientes
    $multas = $conn->query("
        SELECT 
            SUM(CASE WHEN pagada = 'si' THEN monto ELSE 0 END) AS pagadas,
            SUM(CASE WHEN pagada = 'no' THEN monto ELSE 0 END) AS pendientes
        FROM Multa
    ")->fetch_assoc();

    // Totales generales
    $totalPrestamos = $conn->query("SELECT COUNT(*) AS c FROM Prestamo")->fetch_assoc()['c'];
    $totalUsuarios  = $conn->query("SELECT COUNT(*) AS c FROM Usuario WHERE activo = 'si'")->fetch_assoc()['c'];
    $totalMateriales = $conn->query("SELECT COUNT(*) AS c FROM Material")->fetch_assoc()['c'];
    $totalServicios  = $conn->query("SELECT COUNT(*) AS c FROM Servicio")->fetch_assoc()['c'];

// ── Datos para Alumno/Docente ───────────────────────────────
} else {

    // Préstamos del usuario
    $misPrestamos = $conn->query("
        SELECT p.estado, COUNT(*) AS total
        FROM Prestamo p
        WHERE p.idUsuario = '$idUsuario'
        GROUP BY p.estado
    ")->fetch_all(MYSQLI_ASSOC);

    // Historial completo
    $historial = $conn->query("
        SELECT m.titulo, p.fechaPrestamo, p.fechaDevolucion, p.estado
        FROM Prestamo p
        JOIN Ejemplar e ON p.idEjemplar = e.idEjemplar
        JOIN Material m ON e.idMaterial = m.idMaterial
        WHERE p.idUsuario = '$idUsuario'
        ORDER BY p.fechaPrestamo DESC
    ")->fetch_all(MYSQLI_ASSOC);

    // Multas del usuario
    $misMultas = $conn->query("
        SELECT mu.monto, mu.pagada, mu.fechaGenerada, m.titulo
        FROM Multa mu
        JOIN Prestamo p ON mu.idPrestamo = p.idPrestamo
        JOIN Ejemplar e ON p.idEjemplar = e.idEjemplar
        JOIN Material m ON e.idMaterial = m.idMaterial
        WHERE p.idUsuario = '$idUsuario'
        ORDER BY mu.fechaGenerada DESC
    ")->fetch_all(MYSQLI_ASSOC);

    $totalMisPrestamos = $conn->query("SELECT COUNT(*) AS c FROM Prestamo WHERE idUsuario = '$idUsuario'")->fetch_assoc()['c'];
    $totalMisMultas    = $conn->query("SELECT COALESCE(SUM(monto),0) AS c FROM Multa mu JOIN Prestamo p ON mu.idPrestamo = p.idPrestamo WHERE p.idUsuario = '$idUsuario' AND mu.pagada = 'no'")->fetch_assoc()['c'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIBEM - Estadísticas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../home/diseno.css">
    <link rel="stylesheet" href="diseno_estadisticas.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<div class="wrapper">
    <aside class="sidebar">
        <div class="sidebar-logo">
            <div class="logo-icon">
                <svg viewBox="0 -960 960 960" fill="currentColor"><path d="M240-80q-33 0-56.5-23.5T160-160v-640q0-33 23.5-56.5T240-880h480q33 0 56.5 23.5T800-800v640q0 33-23.5 56.5T720-80H240Zm0-80h480v-640h-80v280l-100-60-100 60v-280H240v640Zm0 0v-640 640Zm200-360 100-60 100 60-100-60-100 60Z"/></svg>
            </div>
            <span>SIBEM</span>
        </div>
        <nav class="sidebar-nav">
            <button class="nav-btn" onclick="location.href='../home/inicio.php'">
                <svg fill="currentColor" viewBox="0 0 16 16"><path d="M6.5 14.5v-3.505c0-.245.25-.495.5-.495h2c.25 0 .5.25.5.5v3.5a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 1.5 7.5v7a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5"/></svg>
                Inicio
            </button>
            <button class="nav-btn" onclick="location.href='../prestamos/prestamos.php'">
                <svg fill="currentColor" viewBox="0 0 16 16"><path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm9 1.5a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 0-1h-4a.5.5 0 0 0-.5.5M9 8a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 0-1h-4A.5.5 0 0 0 9 8m1 2.5a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 0-1h-3a.5.5 0 0 0-.5.5m-1 2C9 10.567 7.21 9 5 9c-2.086 0-3.8 1.398-3.984 3.181A1 1 0 0 0 2 13h6.96q.04-.245.04-.5M7 6a2 2 0 1 0-4 0 2 2 0 0 0 4 0"/></svg>
                Préstamos
            </button>
            <?php if (!$esPersonal): ?>
            <button class="nav-btn" onclick="location.href='../usuarios/usuarios.php'">
                <svg fill="currentColor" viewBox="0 0 16 16"><path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"/></svg>
                Usuarios
            </button>
            <?php endif; ?>
            <button class="nav-btn" onclick="location.href='../adeudos/adeudos.php'">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/></svg>
                Adeudos
            </button>
            <button class="nav-btn active">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/></svg>
                Estadísticas
            </button>
            <?php if (!$esPersonal): ?>
            <button class="nav-btn" onclick="location.href='../roles/roles.php'">
                <svg fill="currentColor" viewBox="0 0 20 16"><path d="M8 7a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/><path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1z"/><path d="M16 7l-3.5 1.4v3c0 1.4 1.2 2.5 3.5 2.8 2.3-.3 3.5-1.4 3.5-2.8v-3z" fill="white" stroke="currentColor" stroke-width="0.8"/><path d="M14.2 11l1.1 1.1 2.2-2.2" fill="none" stroke="currentColor" stroke-width="0.9" stroke-linecap="round"/></svg>
                Roles
            </button>
            <?php endif; ?>
        </nav>
        <div class="sidebar-footer">
            <div class="user-row">
                <div class="avatar"><?= strtoupper(substr($_SESSION['nombre'] ?? 'A', 0, 1)) ?></div>
                <div>
                    <div class="user-name"><?= htmlspecialchars($_SESSION['nombre'] ?? 'Usuario') ?></div>
                    <div class="user-role"><?= htmlspecialchars($_SESSION['tipoUsuario'] ?? '') ?></div>
                </div>
                <button class="logout-btn" onclick="confirmarLogout()" title="Cerrar sesión">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/></svg>
                </button>
            </div>
        </div>
    </aside>

    <main class="main-area">
        <div class="topbar">
            <img src="../img/Logo.png" alt="Logo ITSCC" height="50">
            <span class="inst-name">Instituto Tecnológico Superior de Ciudad Constitución</span>
        </div>

        <div class="content-area">

        <?php if ($esAdmin): ?>

            <!-- Tarjetas resumen -->
            <div class="stats-cards">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#eaf3de;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="#27500a"><path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2z"/></svg>
                    </div>
                    <div>
                        <div class="stat-label">Total Préstamos</div>
                        <div class="stat-num"><?= $totalPrestamos ?></div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background:#e6f1fb;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="#185FA5"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/></svg>
                    </div>
                    <div>
                        <div class="stat-label">Usuarios Activos</div>
                        <div class="stat-num"><?= $totalUsuarios ?></div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background:#faeeda;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="#633806"><path d="M18 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM6 4h5v8l-2.5-1.5L6 12V4z"/></svg>
                    </div>
                    <div>
                        <div class="stat-label">Materiales</div>
                        <div class="stat-num"><?= $totalMateriales ?></div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background:#fcebeb;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="#a32d2d"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                    </div>
                    <div>
                        <div class="stat-label">Servicios</div>
                        <div class="stat-num"><?= $totalServicios ?></div>
                    </div>
                </div>
            </div>

            <!-- Gráficas -->
            <!-- Tabs de gráficas -->
            <div class="tabs-container">
                <div class="tabs-header">
                    <button class="tab-btn active" onclick="mostrarTab('tabMeses', this)">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11z"/></svg>
                        Préstamos por mes
                    </button>
                    <button class="tab-btn" onclick="mostrarTab('tabLibros', this)">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM6 4h5v8l-2.5-1.5L6 12V4z"/></svg>
                        Top libros
                    </button>
                    <button class="tab-btn" onclick="mostrarTab('tabUsuarios', this)">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/></svg>
                        Top usuarios
                    </button>
                    <button class="tab-btn" onclick="mostrarTab('tabMultas', this)">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg>
                        Multas
                    </button>
                </div>

                <div class="tab-content" id="tabMeses">
                    <div class="chart-title">Préstamos por mes</div>
                    <canvas id="chartMeses"></canvas>
                </div>
                <div class="tab-content" id="tabLibros" style="display:none;">
                    <div class="chart-title">Top 5 libros más prestados</div>
                    <canvas id="chartLibros"></canvas>
                </div>
                <div class="tab-content" id="tabUsuarios" style="display:none;">
                    <div class="chart-title">Top 5 usuarios con más préstamos</div>
                    <canvas id="chartUsuarios"></canvas>
                </div>
                <div class="tab-content" id="tabMultas" style="display:none;">
                    <div class="chart-title">Multas Registradas</div>
                    <canvas id="chartMultas"></canvas>
                </div>
            </div>

            <?php else: ?>

    <!-- Tarjetas resumen usuario -->
    <div class="stats-cards" style="grid-template-columns: repeat(2, 1fr);">
        <div class="stat-card">
                <div class="stat-icon">
                    <svg width="22" height="22"viewBox="0 -960 960 960">>
                        <path fill="#27500a" d="M240-80q-33 0-56.5-23.5T160-160v-640q0-33 23.5-56.5T240-880h480q33 0 56.5 23.5T800-800v640q0 33-23.5 56.5T720-80H240Zm0-80h480v-640h-80v280l-100-60-100 60v-280H240v640Zm0 0v-640 640Zm200-360 100-60 100 60-100-60-100 60Z"/></svg>
                </div>
                <div>
                <div class="stat-label">Mis Préstamos</div>
                <div class="stat-num"><?= $totalMisPrestamos ?></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="#a32d2d"><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg>
            </div>
            <div>
                <div class="stat-label">Multas Pendientes</div>
                <div class="stat-num">$<?= number_format($totalMisMultas, 2) ?></div>
            </div>
        </div>
    </div>

    <!-- Tabs usuario personal -->
    <div class="tabs-container">
        <div class="tabs-header">
            <button class="tab-btn active" onclick="mostrarTab('tabMisPrestamos', this)">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2z"/></svg>
                Mis préstamos
            </button>
            <button class="tab-btn" onclick="mostrarTab('tabHistorial', this)">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M13 3a9 9 0 1 0 9 9h-2a7 7 0 1 1-7-7v3l4-4-4-4v3z"/></svg>
                Mi historial
            </button>
            <button class="tab-btn" onclick="mostrarTab('tabMisMultas', this)">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg>
                Mis multas
            </button>
        </div>

        <!-- Tab mis préstamos -->
        <div class="tab-content" id="tabMisPrestamos">
            <div class="chart-title">Mis préstamos por estado</div>
            <canvas id="chartMisPrestamos"></canvas>
        </div>

        <!-- Tab historial -->
        <div class="tab-content" id="tabHistorial" style="display:none;">
            <div class="chart-title">Mi historial de préstamos</div>
            <div class="tw">
                <table>
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Fecha préstamo</th>
                            <th>Fecha devolución</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($historial)): ?>
                            <tr><td colspan="4" style="text-align:center;color:#aaa;padding:20px;">No tienes préstamos registrados</td></tr>
                        <?php else: ?>
                            <?php foreach ($historial as $h): ?>
                            <tr>
                                <td><?= htmlspecialchars($h['titulo']) ?></td>
                                <td><?= date('d/m/Y', strtotime($h['fechaPrestamo'])) ?></td>
                                <td><?= date('d/m/Y', strtotime($h['fechaDevolucion'])) ?></td>
                                <td>
                                    <?php if ($h['estado'] === 'activo'): ?>
                                        <span class="badge-activo">Activo</span>
                                    <?php elseif ($h['estado'] === 'vencido'): ?>
                                        <span class="badge-vencido">Vencido</span>
                                    <?php else: ?>
                                        <span class="badge-devuelto">Devuelto</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tab mis multas -->
        <div class="tab-content" id="tabMisMultas" style="display:none;">
            <div class="chart-title">Mis multas</div>
            <div class="tw">
                <table>
                    <thead>
                        <tr>
                            <th>Libro</th>
                            <th>Monto</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($misMultas)): ?>
                            <tr><td colspan="4" style="text-align:center;color:#aaa;padding:20px;">No tienes multas registradas</td></tr>
                        <?php else: ?>
                            <?php foreach ($misMultas as $mu): ?>
                            <tr>
                                <td><?= htmlspecialchars($mu['titulo']) ?></td>
                                <td>$<?= number_format((float)$mu['monto'], 2) ?></td>
                                <td><?= date('d/m/Y', strtotime($mu['fechaGenerada'])) ?></td>
                                <td>
                                    <?php if ($mu['pagada'] === 'si'): ?>
                                        <span class="badge-pagado">Pagada</span>
                                    <?php else: ?>
                                        <span class="badge-pendiente">Pendiente</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

<?php endif; ?>

        </div>
    </main>
</div>

<script>
<?php if ($esAdmin): ?>
// ── Gráfica préstamos por mes ──
const meses = <?= json_encode(array_column($prestamosPorMes, 'mes')) ?>;
const totalesMeses = <?= json_encode(array_column($prestamosPorMes, 'total')) ?>;
new Chart(document.getElementById('chartMeses'), {
    type: 'bar',
    data: {
        labels: meses,
        datasets: [{
            label: 'Préstamos',
            data: totalesMeses,
            backgroundColor: ['#eaf3de','#C0DD97','#3B6D11','#faeeda','#f5e840'],
            borderRadius: 6
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
    }
});

// ── Gráfica top libros ──
const titulosLibros = <?= json_encode(array_column($topLibros, 'titulo')) ?>;
const totalesLibros = <?= json_encode(array_column($topLibros, 'total')) ?>;
new Chart(document.getElementById('chartLibros'), {
    type: 'bar',
    data: {
        labels: titulosLibros,
        datasets: [{
            label: 'Préstamos',
            data: totalesLibros,
            backgroundColor: ['#eaf3de','#C0DD97','#3B6D11','#faeeda','#f5e840'],
            borderRadius: 6
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { x: { beginAtZero: true, ticks: { stepSize: 1 } } }
    }
});

// ── Gráfica top usuarios ──
const nombresUsuarios = <?= json_encode(array_column($topUsuarios, 'nombre')) ?>;
const totalesUsuarios = <?= json_encode(array_column($topUsuarios, 'total')) ?>;
new Chart(document.getElementById('chartUsuarios'), {
    type: 'bar',
    data: {
        labels: nombresUsuarios,
        datasets: [{
            data: totalesUsuarios,
            backgroundColor: ['#3B6D11','#C0DD97','#faeeda','#f5e840','#e6f1fb'],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'bottom', labels: { font: { size: 11 } } } }
    }
});

// ── Gráfica multas ──
new Chart(document.getElementById('chartMultas'), {
    type: 'pie',
    data: {
        labels: ['Pagadas', 'Pendientes'],
        datasets: [{
            data: [<?= $multas['pagadas'] ?>, <?= $multas['pendientes'] ?>],
            backgroundColor: ['#447e14','#efe44d'],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'bottom', labels: { font: { size: 11 } } } }
    }
});

<?php else: ?>
// ── Gráfica mis préstamos ──
const estadosPrestamos = <?= json_encode(array_column($misPrestamos, 'estado')) ?>;
const totalesPrestamos = <?= json_encode(array_column($misPrestamos, 'total')) ?>;
const coloresEstados = estadosPrestamos.map(e => 
    e === 'activo' ? '#C0DD97' : e === 'vencido' ? '#fcebeb' : '#e6f1fb'
);
new Chart(document.getElementById('chartMisPrestamos'), {
    type: 'pie',
    data: {
        labels: estadosPrestamos.map(e => e.charAt(0).toUpperCase() + e.slice(1)),
        datasets: [{
            data: totalesPrestamos,
            backgroundColor:['#4e8521','#f1e758'],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'bottom' } }
    }
});
<?php endif; ?>

function mostrarTab(id, btn) {
    document.querySelectorAll('.tab-content').forEach(t => t.style.display = 'none');
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById(id).style.display = 'block';
    btn.classList.add('active');
}

function confirmarLogout() {
    Swal.fire({
        title: '¿Cerrar sesión?',
        text: '¿Estás seguro que deseas salir del sistema?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3B6D11',
        cancelButtonColor: '#aaa',
        confirmButtonText: 'Sí, cerrar sesión',
        cancelButtonText: 'Cancelar'
    }).then(result => {
        if (result.isConfirmed) {
            location.href = '../../php/php_login/logout.php';
        }
    });
}

</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>