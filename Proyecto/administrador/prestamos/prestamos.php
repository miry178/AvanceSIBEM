<?php
// Conexión
session_start();
require_once '../../bd/conexion.php';

if (!isset($_SESSION['idUsuario'])) {
    header("Location: ../../index.php?error=2");
    exit();
}

$puedeAgregar  = tienePermiso($pdo, $_SESSION['idUsuario'], 'prestamos', 'agregar');
$puedeDevolver = tienePermiso($pdo, $_SESSION['idUsuario'], 'prestamos', 'devolver');
// Contadores para las tarjetas
$totalActivos   = $conn->query("SELECT COUNT(*) AS c FROM Prestamo WHERE estado = 'activo'")->fetch_assoc()['c'];
$totalVencidos  = $conn->query("SELECT COUNT(*) AS c FROM Prestamo WHERE estado = 'vencido'")->fetch_assoc()['c'];
$totalPorVencer = $conn->query("SELECT COUNT(*) AS c FROM Prestamo WHERE estado = 'activo' AND fechaDevolucion BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 3 DAY)")->fetch_assoc()['c'];

// Consulta principal usando la vista
$prestamos = $conn->query("SELECT * FROM vista_prestamos WHERE estado != 'devuelto'");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIBEM - Préstamos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../home/diseno.css">
    <link rel="stylesheet" href="diseno-prestamo.css">
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
            <button class="nav-btn active">
                <svg fill="currentColor" viewBox="0 0 16 16"><path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm9 1.5a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 0-1h-4a.5.5 0 0 0-.5.5M9 8a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 0-1h-4A.5.5 0 0 0 9 8m1 2.5a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 0-1h-3a.5.5 0 0 0-.5.5m-1 2C9 10.567 7.21 9 5 9c-2.086 0-3.8 1.398-3.984 3.181A1 1 0 0 0 2 13h6.96q.04-.245.04-.5M7 6a2 2 0 1 0-4 0 2 2 0 0 0 4 0"/></svg>
                Préstamos
            </button>
            <button class="nav-btn" onclick="location.href='../usuarios/usuarios.php'">
                <svg fill="currentColor" viewBox="0 0 16 16"><path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"/></svg>
                Usuarios
            </button>
            <button class="nav-btn">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/></svg>
                Adeudos
            </button>
            <button class="nav-btn">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/></svg>
                Estadísticas
            </button>
            <button class="nav-btn" onclick="location.href='../roles/roles.php'">
                <svg fill="currentColor" viewBox="0 0 20 16"><path d="M8 7a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/><path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1z"/><path d="M16 7l-3.5 1.4v3c0 1.4 1.2 2.5 3.5 2.8 2.3-.3 3.5-1.4 3.5-2.8v-3z" fill="white" stroke="currentColor" stroke-width="0.8"/><path d="M14.2 11l1.1 1.1 2.2-2.2" fill="none" stroke="currentColor" stroke-width="0.9" stroke-linecap="round"/></svg>
                Roles
            </button> 
            
        </nav>
        <div class="sidebar-footer">
            <div class="user-row">
                <div class="avatar"><?= strtoupper(substr($_SESSION['nombre'] ?? 'A', 0, 1)) ?></div>
                <div>
                    <div class="user-name"><?= htmlspecialchars($_SESSION['nombre'] ?? 'Usuario') ?></div>
                    <div class="user-role"><?= htmlspecialchars($_SESSION['tipoUsuario'] ?? '') ?></div>
                </div>
                <button class="logout-btn" onclick="location.href='../../index.php'" title="Cerrar sesión">
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

            <!-- Mensajes de éxito o error -->
            <?php if (isset($_GET['exito'])): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    Préstamo registrado correctamente
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php elseif (isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?php
                        $errores = [
                            'campos_vacios'  => 'Faltan campos obligatorios',
                            'no_disponible'  => 'El ejemplar ya no está disponible',
                            'insert_fallido' => 'Error al guardar el préstamo'
                        ];
                        echo $errores[$_GET['error']] ?? 'Error desconocido';
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Tarjetas resumen -->
            <div class="prestamo-cards">
                <div class="prestamo-card">
                    <div>
                        <div class="prestamo-card-label">Préstamos Activos</div>
                        <div class="prestamo-card-num"><?= $totalActivos ?></div>
                    </div>
                    <span class="prestamo-card-icon">🕐</span>
                </div>
                <div class="prestamo-card">
                    <div>
                        <div class="prestamo-card-label">Préstamos Vencidos</div>
                        <div class="prestamo-card-num"><?= $totalVencidos ?></div>
                    </div>
                    <span class="prestamo-card-icon">⚠️</span>
                </div>
                <div class="prestamo-card">
                    <div>
                        <div class="prestamo-card-label">Por vencer</div>
                        <div class="prestamo-card-num"><?= $totalPorVencer ?></div>
                    </div>
                    <span class="prestamo-card-icon">📅</span>
                </div>
            <div class="prestamo-card-btn">
                <?php if ($puedeAgregar): ?>
                <button class="add-btn" data-bs-toggle="modal" data-bs-target="#modalAgregarPrestamo">
                    Agregar Préstamo
                </button>
                <?php endif; ?>
            </div>
            </div>

            <!-- Tabla -->
            <div class="tabla-prestamos">
                <h5 class="fw-semibold mb-3">Préstamos Activos</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Título</th>
                                <th>Correo</th>
                                <th>Fecha de préstamo</th>
                                <th>Fecha de devolución</th>
                                <th>Estado</th>
                                <th>Tiempo restante</th>
                                <?php if ($puedeDevolver): ?><th>Acción</th><?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($prestamos && $prestamos->num_rows > 0): ?>
                                <?php while ($row = $prestamos->fetch_assoc()): ?>
                                    <?php
                                        $dias = (int)$row['diasRestantes'];
                                        if ($row['estado'] === 'vencido') {
                                            $badge  = '<span class="badge-vencido">Vencido</span>';
                                            $tiempo = '<span class="text-danger fw-semibold">' . abs($dias) . ' días ven...</span>';
                                        } elseif ($dias <= 1) {
                                            $badge  = '<span class="badge-por-vencer">Por vencer</span>';
                                            $tiempo = '<span class="text-warning fw-semibold">Vence hoy</span>';
                                        } else {
                                            $badge  = '<span class="badge-activo">Activo</span>';
                                            $tiempo = '<span class="text-success">' . $dias . ' días res...</span>';
                                        }
                                        $titulo = strlen($row['titulo']) > 25 ? substr($row['titulo'], 0, 25) . '...' : $row['titulo'];
                                        $correo = strlen($row['correoInst']) > 20 ? substr($row['correoInst'], 0, 20) . '...' : $row['correoInst'];
                                    ?>
                                    <tr>
                                        <td title="<?= htmlspecialchars($row['titulo']) ?>"><?= htmlspecialchars($titulo) ?></td>
                                        <td title="<?= htmlspecialchars($row['correoInst']) ?>"><?= htmlspecialchars($correo) ?></td>
                                        <td><?= date('Y-m-d', strtotime($row['fechaPrestamo'])) ?></td>
                                        <td><?= date('Y-m-d', strtotime($row['fechaDevolucion'])) ?></td>
                                        <td><?= $badge ?></td>
                                        <td><?= $tiempo ?></td>
                                        <td>
                                            <?php if ($puedeDevolver): ?>
                                            <td>
                                                <form method="POST" action="devolver_prestamo.php" style="display:inline">
                                                    <input type="hidden" name="idPrestamo" value="<?= $row['idPrestamo'] ?>">
                                                    <button type="submit" class="btn-devolver">Devolver</button>
                                                </form>
                                            </td>
                                            <?php endif; ?>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        No hay préstamos registrados
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>
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

                    <!-- SECCIÓN USUARIO -->
                    <fieldset class="border rounded p-3 mb-3">
                        <legend class="float-none w-auto px-2 fs-6 text-muted">Datos del Usuario</legend>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Número de Cuenta <span class="text-danger">*</span>
                                </label>
                                <!-- id="inputIdUsuario" y onblur para disparar el AJAX -->
                                <input type="text" id="inputIdUsuario" name="idUsuario"
                                       class="form-control" placeholder="RFC o No. Control"
                                       onblur="buscarUsuario()" required>
                                <small id="msgUsuario" class="text-danger d-none">Usuario no encontrado</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tipo de usuario</label>
                                <!-- readonly: se llena automáticamente con el AJAX -->
                                <input type="text" id="inputTipoUsuario"
                                       class="form-control" placeholder="Se llena automáticamente" readonly>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nombre</label>
                                <input type="text" id="inputNombre"
                                       class="form-control" placeholder="Se llena automáticamente" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Correo</label>
                                <input type="text" id="inputCorreo"
                                       class="form-control" placeholder="Se llena automáticamente" readonly>
                            </div>
                        </div>
                    </fieldset>

                    <!-- SECCIÓN EJEMPLAR -->
                    <fieldset class="border rounded p-3 mb-3">
                        <legend class="float-none w-auto px-2 fs-6 text-muted">Datos del préstamo</legend>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Código del Ejemplar <span class="text-danger"></span>
                                </label>
                                <!-- id="inputCodigo" y onblur para disparar el AJAX -->
                                <input type="text" id="inputCodigo" name="codigoEjemplar"
                                       class="form-control" placeholder="Código del ejemplar"
                                       onblur="buscarEjemplar()" required>
                                <small id="msgEjemplar" class="text-danger d-none">Ejemplar no encontrado</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Título del libro</label>
                                <input type="text" id="inputTitulo"
                                       class="form-control" placeholder="Se llena automáticamente" readonly>
                            </div>
                        </div>
                        <!-- Campo oculto con el idEjemplar real para enviarlo al procesar -->
                        <input type="hidden" id="inputIdEjemplar" name="idEjemplar">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Fecha de préstamo</label>
                                <input type="date" name="fechaPrestamo" id="inputFechaPrestamo"
                                       class="form-control" value="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Fecha de devolución <span class="text-danger"></span>
                                </label>
                                <input type="date" name="fechaDevolucion" class="form-control" required>
                            </div>
                        </div>
                    </fieldset>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-success px-4">Agregar Préstamo</button>
                        <button type="button" class="btn btn-danger px-4" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script>

// ── AJAX: buscar usuario por idUsuario ──
function buscarUsuario() {
    const id  = document.getElementById('inputIdUsuario').value.trim();
    const msg = document.getElementById('msgUsuario');
    if (id === '') return;

    fetch('buscar_usuario.php?id=' + encodeURIComponent(id))
        .then(res => res.json())
        .then(data => {
            if (data.encontrado) {
                document.getElementById('inputNombre').value      = data.nombre;
                document.getElementById('inputCorreo').value      = data.correo;
                document.getElementById('inputTipoUsuario').value = data.tipoPersona;
                msg.classList.add('d-none');

                // Calcular fecha de devolución automáticamente
                if (data.diasPrestamo > 0) {
                    const hoy = new Date();
                    hoy.setDate(hoy.getDate() + data.diasPrestamo);
                    // Formatear a YYYY-MM-DD que necesita el input date
                    const fechaDevolucion = hoy.toISOString().split('T')[0];
                    document.querySelector('[name="fechaDevolucion"]').value = fechaDevolucion;
                }
            } else {
                document.getElementById('inputNombre').value      = '';
                document.getElementById('inputCorreo').value      = '';
                document.getElementById('inputTipoUsuario').value = '';
                document.querySelector('[name="fechaDevolucion"]').value = '';
                msg.classList.remove('d-none');
            }
        });
}

// ── AJAX: buscar ejemplar por código ──
function buscarEjemplar() {
    const codigo = document.getElementById('inputCodigo').value.trim();
    const msg    = document.getElementById('msgEjemplar');
    if (codigo === '') return;

    fetch('buscar_ejemplar.php?codigo=' + encodeURIComponent(codigo))
        .then(res => res.json())
        .then(data => {
            if (data.encontrado && data.disponible) {
                document.getElementById('inputTitulo').value     = data.titulo;
                document.getElementById('inputIdEjemplar').value = data.idEjemplar;
                msg.classList.add('d-none');
            } else {
                document.getElementById('inputTitulo').value     = '';
                document.getElementById('inputIdEjemplar').value = '';
                msg.textContent = data.encontrado
                    ? 'El ejemplar "' + data.titulo + '" ya está prestado'
                    : 'Ejemplar no encontrado';
                msg.classList.remove('d-none');
            }
        });
}

</script>
</body>
</html>
