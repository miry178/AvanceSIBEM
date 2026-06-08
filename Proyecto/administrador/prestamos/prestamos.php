<?php
session_start();
date_default_timezone_set('America/Mazatlan');
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION['idUsuario'])) {
    header("Location: ../../index.php?error=2");
    exit();
}

require_once '../../bd/conexion.php';

$puedeAgregar  = tienePermiso($conn, $_SESSION['idUsuario'], 'prestamos', 'agregar');
$puedeDevolver = tienePermiso($conn, $_SESSION['idUsuario'], 'prestamos', 'devolver');
$esPersonal    = in_array($_SESSION['tipoPersona'] ?? '', ['Alumno', 'Docente']);



// Actualizar préstamos vencidos
$conn->query("
    UPDATE Prestamo 
    SET estado = 'vencido' 
    WHERE estado = 'activo' 
    AND fechaDevolucion < NOW()
");

if ($esPersonal) {
    $idU = $_SESSION['idUsuario'];
    $totalActivos   = $conn->query("SELECT COUNT(*) AS c FROM Prestamo WHERE estado = 'activo' AND idUsuario = '$idU'")->fetch_assoc()['c'];
    $totalVencidos  = $conn->query("SELECT COUNT(*) AS c FROM Prestamo WHERE estado = 'vencido' AND idUsuario = '$idU'")->fetch_assoc()['c'];
    $totalPorVencer = $conn->query("SELECT COUNT(*) AS c FROM Prestamo WHERE estado = 'activo' AND DATE(fechaDevolucion) = CURDATE() AND idUsuario = '$idU'")->fetch_assoc()['c'];
    $prestamos = $conn->query("SELECT * FROM vista_prestamos WHERE estado != 'devuelto' AND idUsuario = '$idU'");
} else {
    $totalActivos   = $conn->query("SELECT COUNT(*) AS c FROM Prestamo WHERE estado = 'activo'")->fetch_assoc()['c'];
    $totalVencidos  = $conn->query("SELECT COUNT(*) AS c FROM Prestamo WHERE estado = 'vencido'")->fetch_assoc()['c'];
    $totalPorVencer = $conn->query("SELECT COUNT(*) AS c FROM Prestamo WHERE estado = 'activo' AND DATE(fechaDevolucion) = CURDATE()")->fetch_assoc()['c'];
    $prestamos = $conn->query("SELECT * FROM vista_prestamos WHERE estado != 'devuelto'");
}

$listaPrestamos = [];
if ($prestamos) {
    while ($row = $prestamos->fetch_assoc()) {
        $listaPrestamos[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIBEM - Préstamos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> 
    <link rel="stylesheet" href="../home/diseno.css">
    <link rel="stylesheet" href="diseno-prestamo.css?v=2">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<?php if (isset($_GET['exito'])): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: 'Préstamo registrado correctamente',
            confirmButtonColor: '#198754'
        });
    });
</script>
<?php elseif (isset($_GET['error'])): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {

    const errores = {
    'campos_vacios':  'Faltan campos obligatorios',
    'no_disponible':  'El ejemplar ya no está disponible',
    'insert_fallido': 'Error al guardar el préstamo',
    'max_prestamos':  'El usuario ya alcanzó el límite máximo de préstamos',
    'tiene_adeudo':   'El usuario tiene multas pendientes',
    'tiene_vencidos': 'El usuario tiene préstamos vencidos'
};
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: errores['<?= $_GET["error"] ?>'] || 'Error desconocido',
        confirmButtonColor: '#dc3545'
    }).then(() => {
        window.history.replaceState({}, document.title, 'prestamos.php');
    });
    });
</script>
<?php endif; ?>

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
            <button class="nav-btn active">
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
            <button class="nav-btn" onclick="location.href='../estadisticas/estadisticas.php'">
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

             <!-- Métricas -->
<div class="metricas">
    <div class="metrica-card">
        <div class="metrica-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="#27500a"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67V7z"/></svg>
        </div>
        <div>
            <div class="metrica-label">Préstamos Activos</div>
            <div class="metrica-num"><?= $totalActivos ?></div>
        </div>
    </div>
    <div class="metrica-card">
        <div class="metrica-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="#f5a623"><path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/></svg>
        </div>
        <div>
            <div class="metrica-label">Préstamos Vencidos</div>
            <div class="metrica-num"><?= $totalVencidos ?></div>
        </div>
    </div>
    <div class="metrica-card">
        <div class="metrica-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="#633806"><path d="M17 12h-5v5h5v-5zM16 1v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2h-1V1h-2zm3 18H5V8h14v11z"/></svg>
        </div>
        <div>
            <div class="metrica-label">Por vencer</div>
            <div class="metrica-num"><?= $totalPorVencer ?></div>
        </div>
    </div>
</div>
            <!-- Toolbar -->
            <div class="toolbar">
                <?php if (!$esPersonal): ?>
                <div class="search-box">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#aaa" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4-4"/></svg>
                    <input type="text" id="searchInput" placeholder="Buscar por título, correo..." oninput="filtrar()">
                </div>
                <?php endif; ?>
                <div style="margin-left:auto; display:flex; gap:8px; align-items:center;">
                    <select class="fsel" id="filtroEstado" onchange="filtrar()">
                        <option value="">Todos los estados</option>
                        <option value="activo">Activo</option>
                        <option value="vencido">Vencido</option>
                    </select>
                    <?php if ($puedeAgregar): ?>
                        <button class="btn-add" data-bs-toggle="modal" data-bs-target="#modalAgregarPrestamo">+ Agregar Préstamo</button>
                    <?php endif; ?>
                </div>
            </div>

            <div class="res-count" id="resCount"></div>
            <!-- Tabla -->
            <div class="tw">
                <table>
                    <thead>
                        <tr>
                            <?php if (!$esPersonal): ?>
                            <th>Título</th>
                            <th>Correo</th>
                            <?php else: ?>
                            <th>Título</th>
                            <?php endif; ?>
                            <th>Fecha de préstamo</th>
                            <th>Fecha de devolución</th>
                            <th>Estado</th>
                            <th>Tiempo restante</th>
                            <?php if ($puedeDevolver && !$esPersonal): ?><th>Acción</th><?php endif; ?>
                        </tr>
                    </thead>
                    <tbody id="tablaBody">
                        <?php if (empty($listaPrestamos)): ?>
                            <tr><td colspan="7" style="text-align:center;color:#aaa;padding:20px;">No hay préstamos registrados</td></tr>
                        <?php else: ?>
                            <?php foreach ($listaPrestamos as $row):
                                $dias = (int)$row['diasRestantes'];
                                if ($row['estado'] === 'vencido') {
                                    $badge  = '<span class="badge-vencido">Vencido</span>';
                                    $tiempo = '<span style="color:#e24b4a;font-weight:600;">' . abs($dias) . ' días ven...</span>';
                                } elseif ($dias <= 1) {
                                    $badge  = '<span class="badge-por-vencer">Por vencer</span>';
                                    $tiempo = '<span style="color:#ba7517;font-weight:600;">Vence hoy</span>';
                                } else {
                                    $badge  = '<span class="badge-activo">Activo</span>';
                                    $tiempo = '<span style="color:#27500a;">' . $dias . ' días res...</span>';
                                }
                                $titulo = strlen($row['titulo']) > 25 ? substr($row['titulo'], 0, 25) . '...' : $row['titulo'];
                                $correo = strlen($row['correoInst']) > 20 ? substr($row['correoInst'], 0, 20) . '...' : $row['correoInst'];
                            ?>
                            <tr data-estado="<?= $row['estado'] ?>" data-titulo="<?= strtolower($row['titulo']) ?>" data-correo="<?= strtolower($row['correoInst']) ?>">
                                <?php if (!$esPersonal): ?>
                                <td style="font-weight:500;" title="<?= htmlspecialchars($row['titulo']) ?>"><?= htmlspecialchars($titulo) ?></td>
                                <td style="font-size:11px;color:#888;" title="<?= htmlspecialchars($row['correoInst']) ?>"><?= htmlspecialchars($correo) ?></td>
                                <?php else: ?>
                                <td style="font-weight:500;" title="<?= htmlspecialchars($row['titulo']) ?>"><?= htmlspecialchars($titulo) ?></td>
                                <?php endif; ?>
                                <td><?= date('Y-m-d', strtotime($row['fechaPrestamo'])) ?></td>
                                <td><?= date('Y-m-d', strtotime($row['fechaDevolucion'])) ?></td>
                                <td><?= $badge ?></td>
                                <td><?= $tiempo ?></td>
                                <?php if ($puedeDevolver && !$esPersonal): ?>
                                <td>
                                    <button class="btn-devolver" onclick="abrirModalDevolver(
                                        '<?= $row['idPrestamo'] ?>',
                                        '<?= htmlspecialchars(addslashes($row['titulo'])) ?>',
                                        '<?= htmlspecialchars(addslashes($row['correoInst'])) ?>',
                                        '<?= htmlspecialchars(addslashes($row['nombre'] ?? '')) ?>',
                                        '<?= date('Y-m-d', strtotime($row['fechaPrestamo'])) ?>',
                                        '<?= date('Y-m-d', strtotime($row['fechaDevolucion'])) ?>',
                                        '<?= $row['estado'] ?>',
                                        '<?= $row['diasRestantes'] ?>'
                                    )">Devolver</button>
                                </td>
                                <?php endif; ?>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

<!-- MODAL - Agregar Préstamo -->
<div class="modal fade" id="modalAgregarPrestamo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content p-2">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-semibold">Agregar Préstamo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="procesar_prestamo.php">

                    <fieldset class="border rounded p-3 mb-3">
                        <legend class="float-none w-auto px-2 fs-6 text-muted">Datos del Usuario</legend>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Número de Cuenta <span class="text-danger">*</span></label>
                                <input type="text" id="inputIdUsuario" name="idUsuario"
                                    class="form-control" placeholder="RFC o No. Control"
                                    onblur="buscarUsuario()"
                                    onkeydown="if(event.key==='Enter'){event.preventDefault();buscarUsuario();}"
                                    required>
                                <small id="msgUsuario" class="text-danger d-none">Usuario no encontrado</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tipo de usuario</label>
                                <input type="text" id="inputTipoUsuario" class="form-control campo-bloqueado" placeholder="Se llena automáticamente" disabled>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nombre</label>
                                <input type="text" id="inputNombre" class="form-control campo-bloqueado" placeholder="Se llena automáticamente" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Correo</label>
                                <input type="text" id="inputCorreo" class="form-control campo-bloqueado" placeholder="Se llena automáticamente" disabled>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset class="border rounded p-3 mb-3">
                        <legend class="float-none w-auto px-2 fs-6 text-muted">Datos del préstamo</legend>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">ISBN <span class="text-danger">*</span></label>
                                <input type="text" id="inputIsbn" name="isbn"
                                    class="form-control" placeholder="ISBN del libro"
                                    onblur="buscarEjemplar()"
                                    onkeydown="if(event.key==='Enter'){event.preventDefault();buscarEjemplar();}"
                                    required>
                                <small id="msgEjemplar" class="text-danger d-none">Ejemplar no encontrado</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Título</label>
                                <input type="text" id="inputTitulo" class="form-control campo-bloqueado" placeholder="Se llena automáticamente" disabled>
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Autor</label>
                                <input type="text" id="inputAutor" class="form-control campo-bloqueado" placeholder="Se llena automáticamente" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Editorial</label>
                                <input type="text" id="inputEditorial" class="form-control campo-bloqueado" placeholder="Se llena automáticamente" disabled>
                            </div>
                        </div>
                        <!-- Campos hidden: estos son los que realmente se envían al servidor -->
                        <input type="hidden" id="inputIdEjemplar"       name="idEjemplar">
                        <input type="hidden" id="inputFechaPrestamo"    name="fechaPrestamo"   value="<?= date('Y-m-d') ?>">
                        <input type="hidden" id="inputFechaDevolucion"  name="fechaDevolucion">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Fecha de préstamo</label>
                                <input type="text" class="form-control campo-bloqueado" value="<?= date('d/m/Y') ?>" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Fecha de devolución <span class="text-danger">*</span></label>
                                <input type="text" id="inputFechaDevolucionVisible" class="form-control campo-bloqueado" placeholder="Se llena automáticamente" disabled>
                            </div>
                        </div>
                    </fieldset>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-success px-4">Agregar Préstamo</button>
                        <button type="button" class="btn btn-danger px-4" data-bs-dismiss="modal" onclick="limpiarModal()">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

 <!-- MODAL - Devolver Préstamo -->
<div class="mbg" id="mbgDevolver">
    <div class="modal-sibem modal-sm">
        <div class="mh">
            <h2>Confirmar devolución</h2>
            <button class="mx" onclick="cerrarModalDevolver()">&times;</button>
        </div>
        <div id="detalleDevolver"></div>
        <div class="form-btns">
            <button class="btn-yel" onclick="cerrarModalDevolver()">Cancelar</button>
            <button class="btn-grn" onclick="confirmarDevolucion()">Confirmar devolución</button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<script>
function filtrar() {
    const q = document.getElementById('searchInput').value.toLowerCase();
    const e = document.getElementById('filtroEstado').value.toLowerCase();
    const filas = document.querySelectorAll('#tablaBody tr[data-estado]');
    let count = 0;
    filas.forEach(tr => {
        const titulo = tr.dataset.titulo || '';
        const correo = tr.dataset.correo || '';
        const estado = tr.dataset.estado || '';
        const okQ = !q || titulo.includes(q) || correo.includes(q);
        const okE = !e || estado === e;
        tr.style.display = (okQ && okE) ? '' : 'none';
        if (okQ && okE) count++;
    });

    // Mostrar mensaje si no hay resultados
    const tbody = document.getElementById('tablaBody');
    const sinResultados = document.getElementById('sinResultadosPrestamos');
    if (count === 0) {
        if (!sinResultados) {
            const tr = document.createElement('tr');
            tr.id = 'sinResultadosPrestamos';
            tr.innerHTML = '<td colspan="7" style="text-align:center;color:#aaa;padding:20px;">No se encontraron préstamos</td>';
            tbody.appendChild(tr);
        }
    } else {
        if (sinResultados) sinResultados.remove();
    }

    document.getElementById('resCount').textContent = count + ' préstamo' + (count !== 1 ? 's' : '') + ' encontrado' + (count !== 1 ? 's' : '');
}

function buscarUsuario() {
    const id  = document.getElementById('inputIdUsuario').value.trim();
    const msg = document.getElementById('msgUsuario');
    if (id === '') return;
    fetch('buscar_usuario.php?id=' + encodeURIComponent(id))
        .then(res => res.json())
        .then(data => {
            if (data.encontrado) {
                // Verificar multas pendientes
                if (data.tieneMulta) {
                    msg.textContent = 'Usuario con multas pendientes, NO puede realizar préstamos';
                    msg.classList.remove('d-none');
                    document.getElementById('inputNombre').value      = '';
                    document.getElementById('inputCorreo').value      = '';
                    document.getElementById('inputTipoUsuario').value = '';
                    document.querySelector('[name="fechaDevolucion"]').value = '';
                    return;
                }

                if (data.tieneVencidos) {
                    msg.textContent = 'El usuario tiene préstamos vencidos, NO puede realizar préstamos';
                    msg.classList.remove('d-none');
                    document.getElementById('inputNombre').value      = '';
                    document.getElementById('inputCorreo').value      = '';
                    document.getElementById('inputTipoUsuario').value = '';
                    document.querySelector('[name="fechaDevolucion"]').value = '';
                    return;
                }

                // Verificar máximo de préstamos
                if (data.excedeMax) {
                    msg.textContent = 'Usuario con maximo de prestamos activos, NO puede realizar más';
                    msg.classList.remove('d-none');
                    document.getElementById('inputNombre').value      = '';
                    document.getElementById('inputCorreo').value      = '';
                    document.getElementById('inputTipoUsuario').value = '';
                    document.querySelector('[name="fechaDevolucion"]').value = '';
                    return;
                }
                document.getElementById('inputNombre').value      = data.nombre;
                document.getElementById('inputCorreo').value      = data.correo;
                document.getElementById('inputTipoUsuario').value = data.tipoPersona;
                msg.classList.add('d-none');
                if (data.diasPrestamo > 0) {
                    // Sumar solo días hábiles (lunes a viernes)
                    const fecha = new Date();
                    let diasRestantes = parseInt(data.diasPrestamo);
                    while (diasRestantes > 0) {
                        fecha.setDate(fecha.getDate() + 1);
                        const dia = fecha.getDay(); // 0=domingo, 6=sábado
                        if (dia !== 0 && dia !== 6) diasRestantes--;
                    }
                    const yyyy = fecha.getFullYear();
                    const mm   = String(fecha.getMonth() + 1).padStart(2, '0');
                    const dd   = String(fecha.getDate()).padStart(2, '0');
                    const fechaStr = `${yyyy}-${mm}-${dd}`;
                    document.getElementById('inputFechaDevolucion').value        = fechaStr;
                    document.getElementById('inputFechaDevolucionVisible').value = `${dd}/${mm}/${yyyy}`;
                }
            } else {
                document.getElementById('inputNombre').value      = '';
                document.getElementById('inputCorreo').value      = '';
                document.getElementById('inputTipoUsuario').value = '';
                document.querySelector('[name="fechaDevolucion"]').value = '';
                msg.textContent = 'Usuario no encontrado';
                msg.classList.remove('d-none');
            }
        });
}
function limpiarModal() {
    document.getElementById('inputIdUsuario').value          = '';
    document.getElementById('inputNombre').value             = '';
    document.getElementById('inputCorreo').value             = '';
    document.getElementById('inputTipoUsuario').value        = '';
    document.getElementById('inputIsbn').value               = '';
    document.getElementById('inputTitulo').value             = '';
    document.getElementById('inputAutor').value              = '';
    document.getElementById('inputEditorial').value          = '';
    document.getElementById('inputIdEjemplar').value         = '';
    document.getElementById('inputFechaDevolucion').value    = '';
    document.getElementById('inputFechaDevolucionVisible').value = '';
    document.getElementById('msgUsuario').classList.add('d-none');
    document.getElementById('msgEjemplar').classList.add('d-none');
}

                                
function buscarEjemplar() {
    const isbn = document.getElementById('inputIsbn').value.trim();
    const msg  = document.getElementById('msgEjemplar');
    if (isbn === '') return;
    fetch('buscar_ejemplar.php?isbn=' + encodeURIComponent(isbn))
        .then(res => res.json())
        .then(data => {
            if (data.encontrado && data.disponible) {
                document.getElementById('inputTitulo').value    = data.titulo;
                document.getElementById('inputAutor').value     = data.autor;
                document.getElementById('inputEditorial').value = data.editorial;
                document.getElementById('inputIdEjemplar').value = data.idEjemplar;
                msg.classList.add('d-none');
            } else {
                document.getElementById('inputTitulo').value    = '';
                document.getElementById('inputAutor').value     = '';
                document.getElementById('inputEditorial').value = '';
                document.getElementById('inputIdEjemplar').value = '';
                msg.textContent = data.encontrado ? 'El libro "' + data.titulo + '" no tiene ejemplares disponibles' : 'ISBN no encontrado';
                msg.classList.remove('d-none');
            }
        });
}


document.addEventListener('DOMContentLoaded', () => {
    const count = document.querySelectorAll('#tablaBody tr[data-estado]').length;
    document.getElementById('resCount').textContent = count + ' préstamo' + (count !== 1 ? 's' : '') + ' encontrado' + (count !== 1 ? 's' : '');
});

// ── Modal devolver ──────────────────────────────────────────────
let idPrestamoActual = null;

function abrirModalDevolver(id, titulo, correo, nombre, fechaPrestamo, fechaDevolucion, estado, dias) {
    idPrestamoActual = id;
    const diasNum = parseInt(dias);
    let estadoHtml = '';
    if (estado === 'vencido') {
        estadoHtml = `<span class="badge-vencido">Vencido — ${Math.abs(diasNum)} días de retraso</span>`;
    } else if (diasNum <= 1) {
        estadoHtml = `<span class="badge-por-vencer">Vence hoy</span>`;
    } else {
        estadoHtml = `<span class="badge-activo">Activo — ${diasNum} días restantes</span>`;
    }

    document.getElementById('detalleDevolver').innerHTML = `
        <div class="mf"><span class="mfk">Título</span><span class="mfv">${titulo}</span></div>
        <div class="mf"><span class="mfk">Usuario</span><span class="mfv">${nombre}</span></div>
        <div class="mf"><span class="mfk">Correo</span><span class="mfv">${correo}</span></div>
        <div class="mf"><span class="mfk">Fecha préstamo</span><span class="mfv">${fechaPrestamo}</span></div>
        <div class="mf"><span class="mfk">Fecha devolución</span><span class="mfv">${fechaDevolucion}</span></div>
        <div class="mf"><span class="mfk">Estado</span><span class="mfv">${estadoHtml}</span></div>
    `;
    document.getElementById('mbgDevolver').classList.add('open');
}

function cerrarModalDevolver() {
    document.getElementById('mbgDevolver').classList.remove('open');
    idPrestamoActual = null;
}

function confirmarDevolucion() {
    if (!idPrestamoActual) return;
    const fd = new FormData();
    fd.append('idPrestamo', idPrestamoActual);
    fetch('devolver_prestamo.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            cerrarModalDevolver();
            if (data.ok) {
                Swal.fire({ icon: 'success', title: '¡Devuelto!', text: data.mensaje, confirmButtonColor: '#2d6a2d', timer: 1800, showConfirmButton: false })
                .then(() => location.reload());
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: data.error, confirmButtonColor: '#dc3545' });
            }
        });
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

document.getElementById('mbgDevolver').addEventListener('click', function(e) {
    if (e.target === this) cerrarModalDevolver();
});

</script>
</script>
</body>
</html>