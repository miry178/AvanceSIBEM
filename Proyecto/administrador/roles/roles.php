<?php

session_start();
require_once '../../bd/conexion.php';


if (!isset($_SESSION['idUsuario'])) {
    header("Location: ../../index.php?error=2");
    exit();
}

header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

$esAdmin = ($_SESSION['tipoUsuario'] ?? '') === 'Administrador';
$esPersonal = in_array($_SESSION['tipoPersona'] ?? '', ['Alumno', 'Docente']);

$roles = $conn->query("SELECT idRol, descripcion FROM Rol ORDER BY idRol")->fetch_all(MYSQLI_ASSOC);

$permisos = $conn->query("SELECT idPermiso, modulo, accion, descripcion FROM Permiso ORDER BY modulo, idPermiso")->fetch_all(MYSQLI_ASSOC);

$modulos = [];
foreach ($permisos as $p) {
    $modulos[$p['modulo']][] = $p;
}

$avatarClass = ['av-1', 'av-2', 'av-3', 'av-4', 'av-5'];

$moduloInfo = [
    'catalogo'     => ['hdr' => 'mh-catalogo',     'label' => 'Catálogo'],
    'prestamos'    => ['hdr' => 'mh-prestamos',    'label' => 'Préstamos'],
    'usuarios'     => ['hdr' => 'mh-usuarios',     'label' => 'Usuarios'],
    'adeudos'      => ['hdr' => 'mh-adeudos',      'label' => 'Adeudos'],
    'estadisticas' => ['hdr' => 'mh-estadisticas', 'label' => 'Estadísticas'],
    'roles'        => ['hdr' => 'mh-roles',        'label' => 'Roles'],
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIBEM - Roles</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../home/diseno.css">
    <link rel="stylesheet" href="diseno_roles.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<div class="wrapper">
    <aside class="sidebar">
        <div class="sidebar-logo">
            <div class="logo-icon">
                <svg viewBox="0 -960 960 960" fill="currentColor"><path d="M240-80q-33 0-56.5-23.5T160-160v-640q0-33 23.5-56.5T240-880h480q33 0 56.5 23.5T800-800v640q0 33-23.5 56.5T720-80H240Zm0-80h480v-640h-80v280l-100-60-100 60v-280H240v640Z"/></svg>
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
            <button class="nav-btn"onclick="location.href='../adeudos/adeudos.php'">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/></svg>
                Adeudos
            </button>
            <button class="nav-btn" onclick="location.href='../estadisticas/estadisticas.php'">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/></svg>
                Estadísticas
            </button>
            <?php if (!$esPersonal): ?>
            <button class="nav-btn active" onclick="location.href='../roles/roles.php'">
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

        <div class="content-area" style="padding:16px;">
            <div class="roles-container">

                <!-- ── Lista izquierda ── -->
                <div class="roles-list">
                    <div class="roles-list-hdr">
                        <span>Roles</span>
                        <?php if ($esAdmin): ?>
                        <button class="btn-nuevo-rol" onclick="abrirModalNuevo()">+ Nuevo Rol</button>
                        <?php endif; ?>
                    </div>
                    <div class="roles-items">
                        <?php foreach ($roles as $i => $rol): ?>
                        <div class="rol-item"
                             data-id="<?= $rol['idRol'] ?>"
                             data-desc="<?= htmlspecialchars($rol['descripcion']) ?>"
                             onclick="seleccionarRol(this)">
                            <div class="rol-avatar <?= $avatarClass[$i % count($avatarClass)] ?>">
                                <?= strtoupper(substr($rol['descripcion'], 0, 2)) ?>
                            </div>
                            <div class="rol-info">
                                <div class="rol-nombre"><?= htmlspecialchars($rol['descripcion']) ?></div>
                                <div class="rol-count" id="count-<?= $rol['idRol'] ?>">— permisos</div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- ── Panel derecho ── -->
                <div class="roles-detail">

                    <!-- Sin selección -->
                    <div class="no-selection" id="noSelection">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        </svg>
                        <span>Selecciona un rol para ver sus permisos</span>
                    </div>

                    <!-- Contenido -->
                    <div class="detail-content" id="detailContent">

                        <div class="detail-hdr">
                            <div>
                                <div class="detail-title" id="detailTitle">—</div>
                                <div class="detail-sub" id="detailSub">—</div>
                            </div>
                        <div class="detail-btns">
                            <?php if ($esAdmin): ?>
                            <button class="btn-eliminar-rol" onclick="eliminarRol()">Eliminar rol</button>
                            <button class="btn-editar-nombre" onclick="abrirModalEditar()">Editar nombre</button>
                            <button class="btn-guardar" onclick="guardarPermisos()">Guardar cambios</button>
                            <?php endif; ?>
                        </div>
                        </div>

                        <!-- Datos -->
                        <div class="datos-row">
                            <div class="fl">
                                <label>Nombre del rol</label>
                                <input type="text" id="fNombre" readonly>
                            </div>
                            <div class="fl">
                                <label>Descripción</label>
                                <input type="text" id="fDescripcion" readonly placeholder="Sin descripción">
                            </div>
                        </div>

                        <!-- Módulos -->
                        <div class="modulos-area">
                            <div class="modulos-grid">
                                <?php foreach ($modulos as $modulo => $perms):
                                    $info = $moduloInfo[$modulo] ?? ['hdr' => 'mh-roles', 'label' => ucfirst($modulo)];
                                ?>
                                <div class="modulo-card">
                                    <div class="modulo-hdr <?= $info['hdr'] ?>">
                                        <span><?= $info['label'] ?></span>
                                    </div>
                                    <div class="modulo-body">
                                        <?php foreach ($perms as $p): ?>
                                        <label class="check-item">
                                            <input type="checkbox"
                                            class="perm-check"
                                            data-id="<?= $p['idPermiso'] ?>"
                                            data-modulo="<?= $modulo ?>"
                                            <?= !$esAdmin ? 'disabled' : '' ?>>
                                            <?= htmlspecialchars($p['descripcion']) ?>
                                        </label>
                                        <?php endforeach; ?>
                                    </div>
                                    <div class="modulo-foot">
                                        <button class="sel-all-btn" data-modulo="<?= $modulo ?>" onclick="toggleModulo('<?= $modulo ?>', this)">
                                            Seleccionar todos
                                        </button>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </main>
</div>

<!-- Modal nuevo / editar rol -->
<div class="mbg" id="mbgRol">
    <div class="modal-sibem">
        <div class="mh">
            <h2 id="modalTitulo">Nuevo rol</h2>
            <button class="mx" onclick="cerrarModal()">&times;</button>
        </div>
        <input type="hidden" id="modalId">
        <div class="fl">
            <label>Nombre del rol *</label>
            <input type="text" id="modalNombre" placeholder="Ej. Bibliotecario" maxlength="50">
        </div>
        <div class="fl">
            <label>Descripción</label>
            <textarea id="modalDesc" rows="2" placeholder="Descripción del rol..."></textarea>
        </div>
        <div class="form-btns">
            <button class="btn-cancel" onclick="cerrarModal()">Cancelar</button>
            <button class="btn-guardar" onclick="guardarRol()">Guardar</button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script>
const modulosData = <?= json_encode($modulos) ?>;
const esAdmin     = <?= $esAdmin ? 'true' : 'false' ?>;

let rolSeleccionado = null;
let permisosRol     = [];
let permisosOriginales = [];

// Cargar contadores al inicio
document.addEventListener('DOMContentLoaded', () => {
    <?php foreach ($roles as $rol): ?>
    fetch('buscar_rol.php?idRol=<?= $rol['idRol'] ?>')
        .then(r => r.json())
        .then(data => {
            const el = document.getElementById('count-<?= $rol['idRol'] ?>');
            if (el) el.textContent = (data.permisos?.length ?? 0) + ' permisos';
        });
    <?php endforeach; ?>
});


function seleccionarRol(el) {
    document.querySelectorAll('.rol-item').forEach(r => r.classList.remove('activo'));
    el.classList.add('activo');
    rolSeleccionado = parseInt(el.dataset.id);
    const desc = el.dataset.desc;

    document.getElementById('noSelection').style.display   = 'none';
    document.getElementById('detailContent').style.display = 'flex';
    document.getElementById('detailTitle').textContent     = desc;
    document.getElementById('detailSub').textContent       = 'Cargando permisos...';
    document.getElementById('fNombre').value               = desc;

    fetch('buscar_rol.php?idRol=' + rolSeleccionado)
        .then(r => r.json())
        .then(data => {
            permisosRol        = (data.permisos || []).map(id => parseInt(id));
            permisosOriginales = [...permisosRol];
            document.getElementById('fDescripcion').value = data.descripcion ?? '';
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    marcarCheckboxes();
                    actualizarSub();
                });
            });
        });
}

function marcarCheckboxes() {
    document.querySelectorAll('.perm-check').forEach(cb => {
        cb.checked = permisosRol.includes(parseInt(cb.dataset.id));
    });
    actualizarBotonesSelAll();
}

function actualizarBotonesSelAll() {
    for (const [modulo, perms] of Object.entries(modulosData)) {
        const ids   = perms.map(p => p.idPermiso);
        const todos = ids.every(id => permisosRol.includes(id));
        const btn   = document.querySelector(`.sel-all-btn[data-modulo="${modulo}"]`);
        if (btn) btn.textContent = todos ? 'Quitar todos' : 'Seleccionar todos';
    }
}

function toggleModulo(modulo, btn) {
    const perms = modulosData[modulo];
    const ids   = perms.map(p => p.idPermiso);
    const todos = ids.every(id => permisosRol.includes(id));
    if (todos) {
        permisosRol = permisosRol.filter(id => !ids.includes(id));
        btn.textContent = 'Seleccionar todos';
    } else {
        ids.forEach(id => { if (!permisosRol.includes(id)) permisosRol.push(id); });
        btn.textContent = 'Quitar todos';
    }
    marcarCheckboxes();
    actualizarSub();
}

document.addEventListener('change', function(e) {
    if (!e.target.classList.contains('perm-check')) return;
    const id = parseInt(e.target.dataset.id);
    if (e.target.checked) {
        if (!permisosRol.includes(id)) permisosRol.push(id);
    } else {
        permisosRol = permisosRol.filter(x => x !== id);
    }
    actualizarBotonesSelAll();
    actualizarSub();
});

function actualizarSub() {
    document.getElementById('detailSub').textContent = permisosRol.length + ' permiso' + (permisosRol.length !== 1 ? 's' : '') + ' activo' + (permisosRol.length !== 1 ? 's' : '');
}

function cancelar() {
    if (rolSeleccionado) {
        permisosRol = [...permisosOriginales];
        marcarCheckboxes();
        actualizarSub();
    }
}

function guardarPermisos() {
    if (!rolSeleccionado) {
        Swal.fire({ icon: 'warning', title: 'Selecciona un rol', confirmButtonColor: '#b8b800' });
        return;
    }
    const fd = new FormData();
    fd.append('idRol', rolSeleccionado);
    fd.append('permisos', JSON.stringify(permisosRol));
    fetch('procesar_rol.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (data.ok) {
                permisosOriginales = [...permisosRol];
                const el = document.getElementById('count-' + rolSeleccionado);
                if (el) el.textContent = permisosRol.length + ' permisos';
                Swal.fire({ icon: 'success', title: '¡Guardado!', text: data.mensaje, confirmButtonColor: '#2d6a2d', timer: 1800, showConfirmButton: false });
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: data.error, confirmButtonColor: '#dc3545' });
            }
        });
}

function eliminarRol() {
    if (!rolSeleccionado) return;
    Swal.fire({
        icon: 'warning',
        title: '¿Eliminar rol?',
        text: 'Se eliminará el rol y sus permisos. Los usuarios con este rol se asignaran como Invitado.',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#aaa',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then(result => {
        if (result.isConfirmed) {
            const fd = new FormData();
            fd.append('idRol', rolSeleccionado);
            fd.append('eliminar', '1');
            fetch('procesar_rol.php', { method: 'POST', body: fd })
                .then(r => r.json())
                .then(data => {
                    if (data.ok) {
                        Swal.fire({ icon: 'success', title: 'Eliminado', text: data.mensaje, confirmButtonColor: '#198754', timer: 1800, showConfirmButton: false })
                        .then(() => location.reload());
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error', text: data.error, confirmButtonColor: '#dc3545' });
                    }
                });
        }
    });
}

function abrirModalNuevo() {
    document.getElementById('modalTitulo').textContent = 'Nuevo rol';
    document.getElementById('modalId').value     = '';
    document.getElementById('modalNombre').value = '';
    document.getElementById('modalDesc').value   = '';
    document.getElementById('mbgRol').classList.add('open');
}

function abrirModalEditar() {
    if (!rolSeleccionado) {
        Swal.fire({ icon: 'warning', title: 'Selecciona un rol primero', confirmButtonColor: '#b8b800' });
        return;
    }
    document.getElementById('modalTitulo').textContent = 'Editar nombre del rol';
    document.getElementById('modalId').value     = rolSeleccionado;
    document.getElementById('modalNombre').value = document.getElementById('fNombre').value;
    document.getElementById('modalDesc').value   = document.getElementById('fDescripcion').value;
    document.getElementById('mbgRol').classList.add('open');
}

function cerrarModal() {
    document.getElementById('mbgRol').classList.remove('open');
}

function guardarRol() {
    const nombre = document.getElementById('modalNombre').value.trim();
    const desc   = document.getElementById('modalDesc').value.trim();
    const id     = document.getElementById('modalId').value;
    if (!nombre) {
        Swal.fire({ icon: 'warning', title: 'Falta el nombre', text: 'Escribe el nombre del rol.', confirmButtonColor: '#b8b800' });
        return;
    }
    const fd = new FormData();
    fd.append('nombre', nombre);
    fd.append('descripcion', desc);
    fd.append('guardarRol', '1');
    if (id) fd.append('idRol', id);
    fetch('procesar_rol.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (data.ok) {
                cerrarModal();
                Swal.fire({ icon: 'success', title: '¡Guardado!', text: data.mensaje, confirmButtonColor: '#198754', timer: 1800, showConfirmButton: false })
                .then(() => location.reload());
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: data.error, confirmButtonColor: '#dc3545' });
            }
        });
}

document.getElementById('mbgRol').addEventListener('click', function(e) {
    if (e.target === this) cerrarModal();
});

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