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

$esPersonal = in_array($_SESSION['tipoPersona'] ?? '', ['Alumno', 'Docente']);

$puedeVer      = tienePermiso($conn, $_SESSION['idUsuario'], 'adeudos', 'ver');
$puedePagar    = tienePermiso($conn, $_SESSION['idUsuario'], 'adeudos', 'pago');
$puedeCondonar = tienePermiso($conn, $_SESSION['idUsuario'], 'adeudos', 'condonar');

if ($esPersonal) {
    $idU = $_SESSION['idUsuario'];

    $totalUsuarios = $conn->query("
        SELECT COUNT(DISTINCT p.idUsuario) AS total
        FROM Multa mu
        JOIN Prestamo p ON mu.idPrestamo = p.idPrestamo
        WHERE mu.pagada = 'no' AND p.idUsuario = '$idU'
    ")->fetch_assoc()['total'];

    $tmp = $conn->query("
        SELECT SUM(CASE WHEN estado='vencido' THEN 1 ELSE 0 END) AS vencidos, COUNT(*) AS total
        FROM Prestamo WHERE idUsuario = '$idU'
    ")->fetch_assoc();

    $totalMultas = $conn->query("
        SELECT COALESCE(SUM(mu.monto),0) AS total
        FROM Multa mu
        JOIN Prestamo p ON mu.idPrestamo = p.idPrestamo
        WHERE mu.pagada='no' AND p.idUsuario = '$idU'
    ")->fetch_assoc()['total'];

    $adeudos = $conn->query("SELECT * FROM vista_adeudos WHERE idUsuario = '$idU' ORDER BY pagada ASC, monto DESC");
} else {
    $totalUsuarios = $conn->query("
        SELECT COUNT(DISTINCT p.idUsuario) AS total
        FROM Multa mu
        JOIN Prestamo p ON mu.idPrestamo = p.idPrestamo
        WHERE mu.pagada = 'no'
    ")->fetch_assoc()['total'];

    $tmp = $conn->query("
        SELECT SUM(CASE WHEN estado='vencido' THEN 1 ELSE 0 END) AS vencidos, COUNT(*) AS total
        FROM Prestamo
    ")->fetch_assoc();

    $totalMultas = $conn->query("
        SELECT COALESCE(SUM(monto),0) AS total FROM Multa WHERE pagada='no'
    ")->fetch_assoc()['total'];

    $adeudos = $conn->query("SELECT * FROM vista_adeudos ORDER BY pagada ASC, monto DESC");
}

$tasaIncumplimiento = ($tmp['total'] > 0)
    ? round(($tmp['vencidos'] / $tmp['total']) * 100) . '%' : '0%';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIBEM - Adeudos</title>
    <link rel="stylesheet" href="../home/diseno.css">
    <link rel="stylesheet" href="../prestamos/diseno-prestamo.css">
    <link rel="stylesheet" href="diseno_adeudos.css">
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
            <button class="nav-btn active">
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
                <button class="logout-btn" title="Cerrar sesión" onclick="confirmarLogout()">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/>
                    </svg>
                </button>
            </div>
        </div>
    </aside>

    <main class="main-area">
        <div class="topbar">
            <img src="../img/Logo.png" alt="Logo ITSCC" height=50>
            <span class="inst-name">Instituto Tecnológico Superior de Ciudad Constitución</span>
        </div>

        <div class="content-area">

            <?php if (isset($_GET['exito'])): ?>
            <script>
                Swal.fire({ icon:'success', title:'¡Pagado!', text:'La multa fue marcada como pagada', confirmButtonColor:'#198754' })
                .then(() => window.history.replaceState({}, document.title, 'adeudos.php'));
            </script>
            <?php elseif (isset($_GET['error'])): ?>
            <script>
                Swal.fire({ icon:'error', title:'Error', text:'No se pudo procesar el pago', confirmButtonColor:'#dc3545' })
                .then(() => window.history.replaceState({}, document.title, 'adeudos.php'));
            </script>
            <?php elseif (isset($_GET['condonada'])): ?>
            <script>
                Swal.fire({ icon:'success', title:'¡Condonada!', text:'La multa fue eliminada del historial', confirmButtonColor:'#198754' })
                .then(() => window.history.replaceState({}, document.title, 'adeudos.php'));
            </script>
            <?php endif; ?>

            <!-- Tarjetas -->
            <div class="metricas">
                <div class="metrica-card">
                    <div class="metrica-icon">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="#27500a"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
                    </div>
                    <div>
                        <div class="metrica-label"><?= $esPersonal ? 'Tienes adeudos' : 'Usuarios con adeudos' ?></div>
                        <div class="metrica-num"><?= $totalUsuarios ?></div>
                    </div>
                </div>
                <div class="metrica-card">
                    <div class="metrica-icon">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="#f5a623"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/></svg>
                    </div>
                    <div>
                        <div class="metrica-label">Tasa de incumplimiento</div>
                        <div class="metrica-num"><?= $tasaIncumplimiento ?></div>
                    </div>
                </div>
                <div class="metrica-card">
                    <div class="metrica-icon">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="#633806"><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg>
                    </div>
                    <div>
                        <div class="metrica-label"><?= $esPersonal ? 'Mi total pendiente' : 'Multas pendientes' ?></div>
                        <div class="metrica-num">$<?= number_format($totalMultas, 2) ?></div>
                    </div>
                </div>
            </div>

            <!-- Toolbar -->
            <div class="toolbar">
                <?php if (!$esPersonal): ?>
                <div class="search-box">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#aaa" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4-4"/></svg>
                    <input type="text" id="buscador" placeholder="Buscar por nombre, No. Control, correo..." oninput="filtrarTabla()">
                </div>
                <select class="fsel" id="filtroTipo" onchange="filtrarTabla()">
                    <option value="">Todos los tipos</option>
                    <option value="alumno">Alumno</option>
                    <option value="docente">Docente</option>
                </select>
                <?php endif; ?>
                <div style="margin-left:auto;">
                    <select class="fsel" id="filtroEstado" onchange="filtrarTabla()">
                        <option value="">Todos los estados</option>
                        <option value="pagado">Pagado</option>
                        <option value="pendiente">Pendiente</option>
                    </select>
                </div>
            </div>

            <div class="res-count" id="contadorResultados"></div>

            <!-- Tabla -->
            <div class="adeudos-panel">
                <table class="adeudos-table">
                    <thead>
                        <tr>
                            <?php if (!$esPersonal): ?>
                            <th>Usuario</th>
                            <th>No. Control / RFC</th>
                            <th>Correo</th>
                            <th>Tipo</th>
                            <?php endif; ?>
                            <th>Libro</th>
                            <th>Fecha de préstamo</th>
                            <th>Fecha de devolución</th>
                            <th>Multa</th>
                            <th>Estado</th>
                            <?php if (!$esPersonal): ?>
                            <th>Acciones</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody id="tablaBody">
                        <?php if ($adeudos && $adeudos->num_rows > 0): ?>
                            <?php while ($row = $adeudos->fetch_assoc()): ?>
                                <?php
                                    $pagado = $row['pagada'] === 'si';
                                    $estadoBadge = $pagado
                                        ? '<span class="badge-pagado">Pagado</span>'
                                        : '<span class="badge-pendiente">Pendiente</span>';

                                    $tipoLower = strtolower($row['tipo']);
                                    if ($tipoLower === 'alumno') {
                                        $tipoBadge = '<span class="badge-tipo-alumno">' . htmlspecialchars($row['tipo']) . '</span>';
                                    } elseif ($tipoLower === 'docente') {
                                        $tipoBadge = '<span class="badge-tipo-docente">' . htmlspecialchars($row['tipo']) . '</span>';
                                    } elseif ($tipoLower === 'encargado') {
                                        $tipoBadge = '<span class="badge-tipo-encargado">' . htmlspecialchars($row['tipo']) . '</span>';
                                    } elseif ($tipoLower === 'invitado') {
                                        $tipoBadge = '<span class="badge-tipo-invitado">' . htmlspecialchars($row['tipo']) . '</span>';
                                    } else {
                                        $tipoBadge = '<span class="badge-tipo-otro">' . htmlspecialchars($row['tipo']) . '</span>';
                                    }

                                    $correo = strlen($row['correo']) > 24
                                        ? substr($row['correo'], 0, 24) . '...'
                                        : $row['correo'];
                                ?>
                                <tr>
                                    <?php if (!$esPersonal): ?>
                                    <td class="td-nombre"><?= htmlspecialchars($row['usuario']) ?></td>
                                    <td class="td-control"><?= htmlspecialchars($row['idUsuario'] ?? '—') ?></td>
                                    <td title="<?= htmlspecialchars($row['correo']) ?>"><?= htmlspecialchars($correo) ?></td>
                                    <td><?= $tipoBadge ?></td>
                                    <?php endif; ?>
                                    <td><?= htmlspecialchars($row['libro']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($row['fechaPrestamo'])) ?></td>
                                    <td><?= date('d/m/Y', strtotime($row['fechaDevolucion'])) ?></td>
                                    <td class="td-monto">$<?= number_format((float)$row['monto'], 2) ?></td>
                                    <td><?= $estadoBadge ?></td>
                                    <?php if (!$esPersonal): ?>
                                    <td>
                                        <div style="display:flex; gap:6px; align-items:center;">
                                            <?php if (!$pagado && $puedePagar): ?>
                                                <button class="btn-pagar"
                                                    onclick="confirmarPago(<?= $row['idMulta'] ?>, '<?= htmlspecialchars($row['usuario']) ?>', '$<?= number_format((float)$row['monto'], 2) ?>')">
                                                    Pagar
                                                </button>
                                            <?php endif; ?>
                                            <?php if (!$pagado && $puedeCondonar): ?>
                                                <button class="btn-condonar"
                                                    onclick="confirmarCondonar(<?= $row['idMulta'] ?>, '<?= htmlspecialchars($row['usuario']) ?>', '$<?= number_format((float)$row['monto'], 2) ?>')">
                                                    Condonar
                                                </button>
                                            <?php endif; ?>
                                            <?php if ($pagado): ?>
                                                <span class="sin-accion">—</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="10" class="td-empty">No hay adeudos registrados</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', actualizarContador);

function filtrarTabla() {
    const txt  = document.getElementById('buscador') ? document.getElementById('buscador').value.toLowerCase() : '';
    const tipo = document.getElementById('filtroTipo') ? document.getElementById('filtroTipo').value.toLowerCase() : '';
    const est  = document.getElementById('filtroEstado').value.toLowerCase();
    let visibles = 0;

    document.querySelectorAll('#tablaBody tr').forEach(function(tr) {
        if (tr.querySelector('.td-empty')) {
   
    }
        const texto     = tr.textContent.toLowerCase();
        const tipoBadge = tr.querySelector('[class*="badge-tipo"]');
        const tipoBadgeTxt = tipoBadge ? tipoBadge.textContent.trim().toLowerCase() : '';
        const estBadge  = tr.querySelector('.badge-pagado, .badge-pendiente');
        const estTxt    = estBadge ? estBadge.textContent.trim().toLowerCase() : '';

        const okT  = texto.includes(txt);
        const okTp = !tipo || tipoBadgeTxt === tipo;
        const okE  = !est  || estTxt === est;
        const mostrar = okT && okTp && okE;
        tr.style.display = mostrar ? '' : 'none';
        if (mostrar) visibles++;
    });

    const tbody = document.getElementById('tablaBody');
    const sinResultados = document.getElementById('sinResultadosAdeudos');
    if (visibles === 0) {
        if (!sinResultados) {
            const tr = document.createElement('tr');
            tr.id = 'sinResultadosAdeudos';
            tr.innerHTML = '<td colspan="10" style="text-align:center;color:#aaa;padding:20px;">No se encontraron adeudos</td>';
            tbody.appendChild(tr);
        }
    } else {
        if (sinResultados) sinResultados.remove();
    }

    actualizarContador();
}

function actualizarContador() {
    const visibles = document.querySelectorAll('#tablaBody tr:not([style*="none"]):not(.td-empty)').length;
    const filaVacia = document.querySelector('#tablaBody .td-empty');
    const el = document.getElementById('contadorResultados');
    if (filaVacia || visibles === 0) {
        if (el) el.textContent = '0 adeudos encontrados';
    } else {
        if (el) el.textContent = visibles + ' adeudo' + (visibles !== 1 ? 's' : '') + ' encontrado' + (visibles !== 1 ? 's' : '');
    }
}
function confirmarPago(idMulta, usuario, monto) {
    Swal.fire({
        title: '¿Confirmar pago?',
        html: `<p>Usuario: <b>${usuario}</b></p><p>Monto: <b>${monto}</b></p>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#198754',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, marcar como pagado',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const form  = document.createElement('form');
            form.method = 'POST';
            form.action = 'pagar_multa.php';
            const input = document.createElement('input');
            input.type  = 'hidden';
            input.name  = 'idMulta';
            input.value = idMulta;
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        }
    });
}

function confirmarCondonar(idMulta, usuario, monto) {
    Swal.fire({
        title: '¿Condonar multa?',
        html: `<p>Usuario: <b>${usuario}</b></p><p>Monto: <b>${monto}</b></p><p style="color:#888;font-size:12px;">La multa será eliminada permanentemente del historial.</p>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e24b4a',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, condonar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const form  = document.createElement('form');
            form.method = 'POST';
            form.action = 'condonar_multa.php';
            const input = document.createElement('input');
            input.type  = 'hidden';
            input.name  = 'idMulta';
            input.value = idMulta;
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
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
</script>
</body>
</html>
<?php $conn->close(); ?>