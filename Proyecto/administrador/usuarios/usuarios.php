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

// Si es alumno o docente no tiene acceso a usuarios
if ($esPersonal) {
    header("Location: ../home/inicio.php");
    exit();
}

$puedeAgregar    = tienePermiso($conn, $_SESSION['idUsuario'], 'usuarios', 'agregar');
$puedeEditar     = tienePermiso($conn, $_SESSION['idUsuario'], 'usuarios', 'editar');
$puedeDesactivar = tienePermiso($conn, $_SESSION['idUsuario'], 'usuarios', 'desactivar');

$carreras   = $conn->query("SELECT idCarrera, descripcion FROM Carrera ORDER BY descripcion")->fetch_all(MYSQLI_ASSOC);
$divisiones = $conn->query("SELECT idDivision, descripcion FROM Division ORDER BY descripcion")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIBEM - Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../home/diseno.css">
    <link rel="stylesheet" href="diseno_usuarios.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<?php if (isset($_GET['exito'])): ?>
<script>
    Swal.fire({ icon:'success', title:'¡Éxito!', text:'<?= htmlspecialchars($_GET["exito"]) ?>', confirmButtonColor:'#198754' })
    .then(()=>{ window.history.replaceState({}, document.title, 'usuarios.php'); });
</script>
<?php elseif (isset($_GET['error'])): ?>
<script>
    Swal.fire({ icon:'error', title:'Error', text:'<?= htmlspecialchars($_GET["error"]) ?>', confirmButtonColor:'#dc3545' })
    .then(()=>{ window.history.replaceState({}, document.title, 'usuarios.php'); });
</script>
<?php endif; ?>

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
        <button class="nav-btn active" onclick="location.href='../usuarios/usuarios.php'">
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
                <button class="logout-btn" title="Cerrar sesión" onclick="confirmarLogout()">
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

            <div class="toolbar">
                <div class="search-box">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#aaa" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4-4"/></svg>
                    <input type="text" id="searchInput" placeholder="Buscar por nombre, No. Control, correo..." oninput="filtrar()">
                </div>
                <!-- Filtro de tipos cargado dinámicamente desde BD -->
                <select class="fsel" id="filtroTipo" onchange="filtrar()">
                    <option value="">Todos los tipos</option>
                    <?php
                    $tiposFiltro = $conn->query("SELECT descripcion FROM Rol ORDER BY idRol");
                    while ($tf = $tiposFiltro->fetch_assoc()) {
                        echo '<option>' . htmlspecialchars($tf['descripcion']) . '</option>';
                    }
                    ?>
                </select>
                <select class="fsel" id="filtroEstado" onchange="filtrar()">
                    <option value="">Todos los estados</option>
                    <option>Activo</option>
                    <option>Inactivo</option>
                </select>
                <?php if ($puedeAgregar): ?>
                    <button class="btn-add" onclick="abrirModalNuevo()">+ Nuevo usuario</button>
                    <?php endif; ?>
                </div>

            <div class="res-count" id="resCount">Cargando usuarios...</div>

            <div class="tw">
                <table>
                    <thead>
                        <tr>
                            <th style="width:22%;">Nombre</th>
                            <th style="width:17%;">No. Control / RFC</th>
                            <th style="width:24%;">Correo</th>
                            <th style="width:13%;">Tipo</th>
                            <th style="width:10%;">Estado</th>
                            <th style="width:14%;"><?php if ($puedeEditar || $puedeDesactivar): ?>Acciones<?php endif; ?></th>
                        </tr>
                    </thead>
                    <tbody id="tablaBody">
                        <tr><td colspan="6" style="text-align:center;color:#aaa;padding:20px;">Cargando...</td></tr>
                    </tbody>
                </table>
            </div>

        </div>
    </main>
</div>

<!-- ── MODAL DETALLE ── -->
<div class="mbg" id="mbgDetalle">
    <div class="modal-sibem modal-sm">
        <div class="mh">
            <h2 id="detTitulo">Detalle del usuario</h2>
            <button class="mx" onclick="cerrar('mbgDetalle')">&times;</button>
        </div>
        <div id="detBody"></div>
        <div class="form-btns">
            <button class="btn-red" onclick="cerrar('mbgDetalle')">Cerrar</button>
            <button class="btn-yel" id="btnEditarDesdeDetalle">Editar</button>
        </div>
    </div>
</div>
<!-- ── MODAL NUEVO / EDITAR ── -->
<div class="mbg" id="mbgForm">
    <div class="modal-sibem">
        <div class="mh">
            <h2 id="formTitulo">Nuevo usuario</h2>
            <button class="mx" onclick="cerrar('mbgForm')">&times;</button>
        </div>

        <fieldset>
            <legend>Datos personales</legend>
            <div class="fg">
                <div class="fl">
                    <label>No. Control / RFC *</label>
                    <input type="text" id="fId" placeholder="Ej. L233110179 o PELJ800101" maxlength="15" oninput="generarCorreo(this.value)" onblur="verificarId(this.value)">
                </div>
                <div class="fl">
                    <label>Nombre completo *</label>
                    <input type="text" id="fNombre" placeholder="Nombre completo">
                </div>
                <div class="fl">
                    <label>Correo institucional *</label>
                    <input type="email" id="fCorreo" placeholder="Ej. LnoControl@cdconstitucion.tecnm.mx">
                </div>
            </div>
        </fieldset>

        <fieldset>
            <legend>Tipo de persona</legend>
            <div class="radio-row">
                <label class="rl">
                    <input type="radio" name="fTipoPersona" value="alumno" onchange="cambiarTipoPersona('alumno')">
                    Alumno
                </label>
                <label class="rl">
                    <input type="radio" name="fTipoPersona" value="docente" onchange="cambiarTipoPersona('docente')">
                    Docente
                </label>
                <label class="rl">
                    <input type="radio" name="fTipoPersona" value="personal" onchange="cambiarTipoPersona('personal')">
                    Personal Bibliotecario
                </label>
            </div>
        </fieldset>

        <!-- Carrera — solo Alumnos -->
        <div class="campo-extra" id="campoCarrera" style="display:none;">
            <div class="fl">
                <label>Carrera *</label>
                <select id="fCarrera">
                    <option value="">Selecciona una carrera</option>
                    <?php foreach ($carreras as $c): ?>
                        <option value="<?= $c['idCarrera'] ?>"><?= htmlspecialchars($c['descripcion']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- División — solo Docentes -->
        <div class="campo-extra" id="campoDivision" style="display:none;">
            <div class="fl">
                <label>División *</label>
                <select id="fDivision">
                    <option value="">Selecciona una división</option>
                    <?php foreach ($divisiones as $d): ?>
                        <option value="<?= $d['idDivision'] ?>"><?= htmlspecialchars($d['descripcion']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Rol — solo Personal -->
    <div id="campoRol" style="display:none;">
        <fieldset>
            <legend>Rol en el sistema</legend>
            <div class="radio-row">
                <?php
                $rolesQuery = $conn->query("SELECT idRol, descripcion FROM Rol WHERE idRol != 1 ORDER BY idRol");
                while ($rol = $rolesQuery->fetch_assoc()) {
                    echo '<label class="rl">
                        <input type="radio" name="fTipo" value="' . $rol['idRol'] . '">
                        ' . htmlspecialchars($rol['descripcion']) . '
                    </label>';
                }
                ?>
            </div>
        </fieldset>
    </div>

        <fieldset>
            <legend>Estado</legend>
            <div class="radio-row">
                <label class="rl"><input type="radio" name="fEstado" value="si" checked> Activo</label>
                <label class="rl"><input type="radio" name="fEstado" value="no"> Inactivo</label>
            </div>
        </fieldset>

        <div class="form-btns">
            <button class="btn-red" id="btnEliminar" style="display:none;" onclick="eliminarUsuario()">Desactivar</button>
            <button class="btn-yel" onclick="cerrar('mbgForm')">Cancelar</button>
            <button class="btn-grn" onclick="guardarUsuario()">Guardar</button>
        </div>
    </div>
</div>


            </div>
        </fieldset>

        <!-- Carrera — solo Alumnos (idTipoPersona = 5) -->
        <div class="campo-extra" id="campoCarrera">
            <div class="fl">
                <label>Carrera *</label>
                <select id="fCarrera">
                    <option value="">Selecciona una carrera</option>
                    <?php foreach ($carreras as $c): ?>
                        <option value="<?= $c['idCarrera'] ?>"><?= htmlspecialchars($c['descripcion']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- División — solo Docentes (idTipoPersona = 4) -->
        <div class="campo-extra" id="campoDivision">
            <div class="fl">
                <label>División *</label>
                <select id="fDivision">
                    <option value="">Selecciona una división</option>
                    <?php foreach ($divisiones as $d): ?>
                        <option value="<?= $d['idDivision'] ?>"><?= htmlspecialchars($d['descripcion']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <fieldset>
            <legend>Estado</legend>
            <div class="radio-row">
                <label class="rl"><input type="radio" name="fEstado" value="si" checked> Activo</label>
                <label class="rl"><input type="radio" name="fEstado" value="no"> Inactivo</label>
            </div>
        </fieldset>

        <div class="form-btns">
            <button class="btn-red" id="btnEliminar" style="display:none;" onclick="eliminarUsuario()">Desactivar</button>
            <button class="btn-yel" onclick="cerrar('mbgForm')">Cancelar</button>
            <button class="btn-grn" onclick="guardarUsuario()">Guardar</button>
        </div>
    </div>
</div>
<script>
const puedeEditar     = <?= $puedeEditar ? 'true' : 'false' ?>;
const puedeDesactivar = <?= $puedeDesactivar ? 'true' : 'false' ?>;
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script>
let usuarioEditando = null;
let todosUsuarios   = [];

function cargarUsuarios() {
    fetch('buscar_usuarios.php')
        .then(r => r.json())
        .then(data => {
            if (data.ok) { todosUsuarios = data.usuarios; filtrar(); }
            else mostrarError('Error al cargar usuarios: ' + data.error);
        })
        .catch(() => mostrarError('Error de conexión'));
}

function filtrar() {
    const q = document.getElementById('searchInput').value.toLowerCase();
    const t = document.getElementById('filtroTipo').value;
    const e = document.getElementById('filtroEstado').value;
    const data = todosUsuarios.filter(u => {
        const mQ = !q || u.nombre.toLowerCase().includes(q) || u.idUsuario.toLowerCase().includes(q) || u.correoInst.toLowerCase().includes(q);
        const mT = !t || u.tipoPersona === t;
        const mE = !e || u.estadoLabel === e;
        return mQ && mT && mE;
    });
    renderTabla(data);
}

const tipoBadge = {
    'Administrador': '<span class="badge" style="background:#faeeda;color:#633806;">Administrador</span>',
    'Encargado':     '<span class="badge" style="background:#eeedfe;color:#3c3489;">Encargado</span>',
    'Administrativo':'<span class="badge bam">Administrativo</span>',
    'Docente':       '<span class="badge bp">Docente</span>',
    'Alumno':        '<span class="badge bb">Alumno</span>',
    'Personal':      '<span class="badge bss">Personal</span>',
    'Invitado':      '<span class="badge" style="background:#f1efe8;color:#444441;">Invitado</span>',
};

function renderTabla(data) {
    const b = document.getElementById('tablaBody');
    document.getElementById('resCount').textContent = data.length + ' usuario' + (data.length !== 1 ? 's' : '') + ' encontrado' + (data.length !== 1 ? 's' : '');
    if (!data.length) {
        b.innerHTML = '<tr><td colspan="6" style="text-align:center;color:#aaa;padding:20px;">No se encontraron usuarios</td></tr>';
        return;
    }
    b.innerHTML = data.map(u => `
        <tr>
            <td style="font-weight:500;">${u.nombre}</td>
            <td style="color:#888;font-size:11px;">${u.idUsuario}</td>
            <td style="font-size:11px;">${u.correoInst}</td>
            <td>${tipoBadge[u.tipoPersona] || '<span class="badge" style="background:#f1efe8;color:#555;">' + u.tipoPersona + '</span>'}</td>
            <td>${u.activo === 'si' ? '<span class="badge ba">Activo</span>' : '<span class="badge bi">Inactivo</span>'}</td>
            <td>
                ${puedeEditar ? `
                <button class="ic-btn" style="background:#fff8c0;" title="Editar"
                    onclick="event.stopPropagation(); abrirModalEditar('${u.idUsuario}')">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#b8b800" stroke-width="2">
                        <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                </button>` : ''}
                ${puedeDesactivar ? `
                <button class="ic-btn" style="background:${u.activo === 'si' ? '#fcebeb' : '#eaf3de'};" 
                    title="${u.activo === 'si' ? 'Desactivar' : 'Activar'}"
                    onclick="event.stopPropagation(); confirmarToggle('${u.idUsuario}', '${u.nombre}', '${u.activo}')">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" 
                        stroke="${u.activo === 'si' ? '#e24b4a' : '#27500a'}" stroke-width="2">
                        ${u.activo === 'si' 
                            ? '<polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/>'
                            : '<path d="M20 6L9 17l-5-5"/>'}
                    </svg>
                </button>` : ''}
            </td>
        </tr>`).join('');
}
function verDetalle(id) {
    const u = todosUsuarios.find(x => x.idUsuario === id);
    if (!u) return;
    document.getElementById('detTitulo').textContent = u.nombre;
    document.getElementById('detBody').innerHTML = [
        ['No. Control / RFC', u.idUsuario],
        ['Correo',            u.correoInst],
        ['Tipo',              u.tipoPersona],
        [u.tipoPersona === 'Alumno' ? 'Carrera' : u.tipoPersona === 'Docente' ? 'División' : 'Área', u.extra || '—'],
        ['Estado', u.activo === 'si' ? 'Activo' : 'Inactivo'],
    ].map(([k, v]) => `<div class="mf"><span class="mfk">${k}</span><span class="mfv">${v}</span></div>`).join('');
    document.getElementById('btnEditarDesdeDetalle').onclick = () => { cerrar('mbgDetalle'); abrirModalEditar(id); };
    document.getElementById('mbgDetalle').classList.add('open');
}

function abrirModalNuevo() {
    usuarioEditando = null;
    document.getElementById('formTitulo').textContent = 'Nuevo usuario';
    document.getElementById('fId').value      = '';
    document.getElementById('fNombre').value  = '';
    document.getElementById('fCorreo').value  = '';
    document.getElementById('fId').readOnly   = false;
    document.querySelectorAll('input[name="fTipoPersona"]').forEach(r => r.checked = false);
    document.querySelectorAll('input[name="fTipo"]').forEach(r => r.checked = false);
    document.querySelectorAll('input[name="fEstado"]').forEach(r => r.checked = r.value === 'si');
    document.getElementById('campoCarrera').style.display  = 'none';
    document.getElementById('campoDivision').style.display = 'none';
    document.getElementById('campoRol').style.display      = 'none';
    document.getElementById('btnEliminar').style.display   = 'none';
    document.getElementById('mbgForm').classList.add('open');
}

function abrirModalEditar(id) {
    const u = todosUsuarios.find(x => x.idUsuario === id);
    if (!u) return;
    usuarioEditando = id;
    document.getElementById('formTitulo').textContent = 'Editar usuario — ' + u.nombre;
    document.getElementById('fId').value     = u.idUsuario;
    document.getElementById('fNombre').value = u.nombre;
    document.getElementById('fCorreo').value = u.correoInst;
    document.getElementById('fId').readOnly  = true;
    document.querySelectorAll('input[name="fTipo"]').forEach(r => r.checked = r.value === String(u.idTipoPersona));
    document.querySelectorAll('input[name="fEstado"]').forEach(r => r.checked = r.value === u.activo);
    cambiarTipo(u.idTipoPersona);
    if (u.idCarrera)  document.getElementById('fCarrera').value  = u.idCarrera;
    if (u.idDivision) document.getElementById('fDivision').value = u.idDivision;
    document.getElementById('btnEliminar').style.display = 'inline-flex';
    document.getElementById('mbgForm').classList.add('open');
}

function generarCorreo(valor) {
    let id = valor.trim();
    if (id !== '') {
        let prefijo = /^\d/.test(id) ? 'L' + id : id;
        document.getElementById('fCorreo').value = prefijo + '@cdconstitucion.tecnm.mx';
    } else {
        document.getElementById('fCorreo').value = '';
    }
}

function verificarId(valor) {
    if (!valor.trim() || usuarioEditando) return;
    fetch('buscar_usuarios.php')
        .then(r => r.json())
        .then(data => {
            const existe = data.usuarios.find(u => u.idUsuario.toLowerCase() === valor.trim().toLowerCase());
            if (existe) {
                Swal.fire({ icon:'warning', title:'Usuario ya existe', text:'El No. Control / RFC "' + valor.trim() + '" ya está registrado.', confirmButtonColor:'#b8b800' });
                document.getElementById('fId').value     = '';
                document.getElementById('fCorreo').value = '';
                document.getElementById('fId').focus();
            }
        });
}

function cambiarTipoPersona(tipo) {
    // Ocultar todos los campos extra primero
    document.getElementById('campoCarrera').style.display  = 'none';
    document.getElementById('campoDivision').style.display = 'none';
    document.getElementById('campoRol').style.display      = 'none';

    // Limpiar selección de rol
    document.querySelectorAll('input[name="fTipo"]').forEach(r => r.checked = false);

    if (tipo === 'alumno') {
        // Alumno → mostrar carrera, asignar rol Invitado automáticamente
        document.getElementById('campoCarrera').style.display = 'block';
        const invitado = document.querySelector('input[name="fTipo"][value="3"]');
        if (invitado) invitado.checked = true;
    } else if (tipo === 'docente') {
        // Docente → mostrar división, asignar rol Invitado automáticamente
        document.getElementById('campoDivision').style.display = 'block';
        const invitado = document.querySelector('input[name="fTipo"][value="3"]');
        if (invitado) invitado.checked = true;
    } else if (tipo === 'personal') {
        // Personal → mostrar selector de rol
        document.getElementById('campoRol').style.display = 'block';
    }
}

function guardarUsuario() {
    const id       = document.getElementById('fId').value.trim();
    const nombre   = document.getElementById('fNombre').value.trim();
    const correo   = document.getElementById('fCorreo').value.trim();
    const tipoEl   = document.querySelector('input[name="fTipo"]:checked');
    const estadoEl = document.querySelector('input[name="fEstado"]:checked');

    if (!id || !nombre || !correo || !tipoEl) {
    Swal.fire({ icon:'warning', title:'Campos incompletos', text:'Llena todos los campos obligatorios.', confirmButtonColor:'#b8b800' });
    return;
}
    // Validar que el nombre solo tenga letras
    const soloLetras = /^[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ\s.'-]+$/;
    if (!soloLetras.test(nombre)) {
        Swal.fire({ icon:'warning', title:'Nombre inválido', text:'El nombre solo debe contener letras.', confirmButtonColor:'#b8b800' });
        return;
    }

    const tipo     = tipoEl.value;
    const estado   = estadoEl ? estadoEl.value : 'si';
    const carrera  = document.getElementById('fCarrera').value;
    const division = document.getElementById('fDivision').value;

    // Alumno = 5, Docente = 4
    if (tipo === '5' && !carrera) {
        Swal.fire({ icon:'warning', title:'Falta la carrera', text:'Selecciona una carrera para el alumno.', confirmButtonColor:'#b8b800' }); return;
    }
    if (tipo === '4' && !division) {
        Swal.fire({ icon:'warning', title:'Falta la división', text:'Selecciona una división para el docente.', confirmButtonColor:'#b8b800' }); return;
    }

    const fd = new FormData();
    fd.append('idUsuario',     id);
    fd.append('nombre',        nombre);
    fd.append('correoInst',    correo);
    fd.append('idTipoPersona', tipo);
    fd.append('activo',        estado);
    fd.append('idCarrera',     carrera);
    fd.append('idDivision',    division);
    fd.append('editando',      usuarioEditando || '');

    fetch('procesar_usuario.php', { method:'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (data.ok) {
                cerrar('mbgForm');
                Swal.fire({ icon:'success', title:'¡Éxito!', text: data.mensaje, confirmButtonColor:'#198754' })
                .then(() => cargarUsuarios());
            } else {
                Swal.fire({ icon:'error', title:'Error', text: data.error, confirmButtonColor:'#dc3545' });
            }
        })
        .catch(() => Swal.fire({ icon:'error', title:'Error', text:'Error de conexión', confirmButtonColor:'#dc3545' }));
}

function confirmarToggle(id, nombre, estadoActual) {
    const activando = estadoActual === 'no';
    Swal.fire({
        icon: 'warning',
        title: activando ? '¿Activar usuario?' : '¿Desactivar usuario?',
        text: activando 
            ? nombre + ' volverá a estar activo en el sistema.'
            : nombre + ' quedará como inactivo pero se conservará su historial.',
        showCancelButton: true,
        confirmButtonColor: activando ? '#198754' : '#b8b800',
        cancelButtonColor: '#aaa',
        confirmButtonText: activando ? 'Sí, activar' : 'Sí, desactivar',
        cancelButtonText: 'Cancelar'
    }).then(result => {
        if (result.isConfirmed) {
            const fd = new FormData();
            fd.append('idUsuario', id);
            fd.append('activo', activando ? 'si' : 'no');
            fd.append('toggle', '1');
            fetch('procesar_usuario.php', { method:'POST', body: fd })
                .then(r => r.json())
                .then(data => {
                    if (data.ok) {
                        Swal.fire({ 
                            icon:'success', 
                            title: activando ? 'Activado' : 'Desactivado', 
                            text: data.mensaje, 
                            confirmButtonColor:'#198754' 
                        }).then(() => cargarUsuarios());
                    } else {
                        Swal.fire({ icon:'error', title:'Error', text: data.error, confirmButtonColor:'#dc3545' });
                    }
                });
        }
    });
}

function eliminarUsuario() {
    if (usuarioEditando) {
        const u = todosUsuarios.find(x => x.idUsuario === usuarioEditando);
        confirmarToggle(usuarioEditando, document.getElementById('fNombre').value, u ? u.activo : 'si');
    }
}

function cerrar(id) { document.getElementById(id).classList.remove('open'); }

function mostrarError(msg) {
    document.getElementById('tablaBody').innerHTML = '<tr><td colspan="6" style="text-align:center;color:#e24b4a;padding:20px;">' + msg + '</td></tr>';
}

document.querySelectorAll('.mbg').forEach(m => m.addEventListener('click', function(e) {
    if (e.target === this) cerrar(this.id);
}));

document.addEventListener('DOMContentLoaded', cargarUsuarios);

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