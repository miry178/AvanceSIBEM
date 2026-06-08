<?php
session_start();

require_once '../../bd/conexion.php';

if (!isset($_SESSION['idUsuario'])) {
    header("Location: ../../index.php?error=2");
    exit();
}

// Verificar permisos
$puedeAgregar = tienePermiso($conn, $_SESSION['idUsuario'], 'catalogo', 'agregar');
$esPersonal = in_array($_SESSION['tipoPersona'] ?? '', ['Alumno', 'Docente']);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIBEM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="diseno.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
   
</head>
<body>

<?php if (isset($_GET['exito'])): ?>
<script>
    Swal.fire({
        icon: 'success',
        title: '¡Éxito!',
        text: 'Material agregado correctamente',
        confirmButtonColor: '#198754'
    }).then(() => {
        window.history.replaceState({}, document.title, 'inicio.php');
    });
</script>
<?php elseif (isset($_GET['error'])): ?>
<script>
    <?php
        $errores = [
            'campos_vacios'  => 'Faltan campos obligatorios',
            'insert_fallido' => 'Error al guardar el material'
        ];
        $msg = $errores[$_GET['error']] ?? 'Error desconocido';
    ?>
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: '<?= $msg ?>',
        confirmButtonColor: '#dc3545'
    }).then(() => {
        window.history.replaceState({}, document.title, 'inicio.php');
    });
</script>
<?php endif; ?>

<div class="wrapper">

    <!-- ── SIDEBAR ── -->
    <aside class="sidebar">
        <div class="sidebar-logo">
            <div class="logo-icon">
                <svg viewBox="0 -960 960 960" fill="currentColor"><path d="M240-80q-33 0-56.5-23.5T160-160v-640q0-33 23.5-56.5T240-880h480q33 0 56.5 23.5T800-800v640q0 33-23.5 56.5T720-80H240Zm0-80h480v-640h-80v280l-100-60-100 60v-280H240v640Zm0 0v-640 640Zm200-360 100-60 100 60-100-60-100 60Z"/></svg>
            </div>
            <span>SIBEM</span>
        </div>

        <nav class="sidebar-nav">
            <button class="nav-btn active" onclick="seleccionarBoton(this); location.href='inicio.php'">
                <svg fill="currentColor" viewBox="0 0 16 16"><path d="M6.5 14.5v-3.505c0-.245.25-.495.5-.495h2c.25 0 .5.25.5.5v3.5a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 1.5 7.5v7a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5"/></svg>
                Inicio
            </button>
            <button class="nav-btn" onclick="seleccionarBoton(this); location.href='../prestamos/prestamos.php'">
                <svg fill="currentColor" viewBox="0 0 16 16"><path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm9 1.5a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 0-1h-4a.5.5 0 0 0-.5.5M9 8a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 0-1h-4A.5.5 0 0 0 9 8m1 2.5a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 0-1h-3a.5.5 0 0 0-.5.5m-1 2C9 10.567 7.21 9 5 9c-2.086 0-3.8 1.398-3.984 3.181A1 1 0 0 0 2 13h6.96q.04-.245.04-.5M7 6a2 2 0 1 0-4 0 2 2 0 0 0 4 0"/></svg>
                Préstamos
            </button>
            <?php if (!$esPersonal): ?>
            <button class="nav-btn" onclick="seleccionarBoton(this); location.href='../usuarios/usuarios.php'">
                <svg fill="currentColor" viewBox="0 0 16 16"><path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"/></svg>
                Usuarios
            </button>
            <?php endif; ?>

            <button class="nav-btn" onclick="seleccionarBoton(this); location.href='../adeudos/adeudos.php'">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/></svg>
                Adeudos
            </button>
            <button class="nav-btn" onclick="seleccionarBoton(this); location.href='../estadisticas/estadisticas.php'">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/></svg>
                Estadísticas
            </button>
            <?php if (!$esPersonal): ?>
            <button class="nav-btn"  onclick="seleccionarBoton(this); location.href='../roles/roles.php'">
                <svg fill="currentColor" viewBox="0 0 20 16"><path d="M8 7a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/><path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1z"/><path d="M16 7l-3.5 1.4v3c0 1.4 1.2 2.5 3.5 2.8 2.3-.3 3.5-1.4 3.5-2.8v-3z" fill="white" stroke="currentColor" stroke-width="0.8"/><path d="M14.2 11l1.1 1.1 2.2-2.2" fill="none" stroke="currentColor" stroke-width="0.9" stroke-linecap="round"/></svg>
                Roles
            </button>
            <?php endif; ?>
        </nav>
        <!-- Obtenemos los datos del usuario para mostrar el nombre y rol del que se loguea -->
        <div class="sidebar-footer">
            <div class="user-row">
                <div class="avatar"><?= strtoupper(substr($_SESSION['nombre'] ?? 'A', 0, 1)) ?></div>
                    <div>
                        <!--Esas funciones de js convierte caracteres extraños en seguros-->
                        <div class="user-name"><?= htmlspecialchars($_SESSION['nombre'] ?? 'Usuario') ?></div>
                        <div class="user-role"><?= htmlspecialchars($_SESSION['tipoUsuario'] ?? '') ?></div>
                    </div>
                <button class="logout-btn" onclick="confirmarLogout()" title="Cerrar sesión">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/></svg>
                </button>
            </div>
        </div>
    </aside>

    <!-- ── ÁREA PRINCIPAL ── -->
    <main class="main-area">

        <div class="topbar">
            <img src="../img/Logo.png" alt="Logo ITSCC" height="50">
            <span class="inst-name">Instituto Tecnológico Superior de Ciudad Constitución</span>
        </div>

        <div class="content-area">

            <!-- ── Buscador y filtros ── -->
            <div class="toolbar" style="flex-direction:column; gap:10px;">

                <!-- Fila 1: input + botón -->
                <div style="display:flex; gap:8px; align-items:center; width:100%;">
                    <div class="search-box" style="flex:1;">
                        <svg viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
                        <input type="text" id="buscador" placeholder="Escribe aquí para buscar..." autocomplete="off" oninput="buscarMaterial()">
                    </div>
                    <?php if ($puedeAgregar): ?>
                        <button class="add-btn" onclick="cargarFormulario()">+ Agregar Material</button>
                    <?php endif; ?>
                    </div>

                <!-- Fila 2: chips + selects -->
                <div class="filtros-row">
                    <button class="chip-filtro active" data-campo="todo"      onclick="setChip(this)">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M15.5 14h-.79l-.28-.27A6.5 6.5 0 1 0 14 15.5l.27.28v.79l5 5L20.49 19l-5-5zm-6 0C7.01 14 5 12 5 9.5S7.01 5 9.5 5 14 7 14 9.5 12 14 9.5 14z"/></svg>
                        Todo
                    </button>
                    <button class="chip-filtro" data-campo="titulo"    onclick="setChip(this)">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M3 9h14V7H3v2zm0 4h14v-2H3v2zm0 4h7v-2H3v2zm16 0v-4h2v4h-2zm0-6h2v2h-2v-2zm0-4h2v2h-2V7z"/></svg>
                        Título
                    </button>
                    <button class="chip-filtro" data-campo="autor"     onclick="setChip(this)">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/></svg>
                        Autor
                    </button>
                    <button class="chip-filtro" data-campo="isbn"      onclick="setChip(this)">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M3 5h18v2H3V5zm0 4h18v2H3V9zm0 4h18v2H3v-2zm0 4h18v2H3v-2z"/></svg>
                        ISBN
                    </button>
                    <button class="chip-filtro" data-campo="editorial" onclick="setChip(this)">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM6 4h5v8l-2.5-1.5L6 12V4z"/></svg>
                        Editorial
                    </button>

                    <!-- Categorías cargadas dinámicamente desde la BD -->
                    <select class="filter-select" id="filtroClasificacion" onchange="buscarMaterial()">
                        <option value="">Todas las clasificaciones</option>
                        <?php
                            // Áreas
                            $areas = $conn->query("SELECT descripcion FROM Area ORDER BY descripcion");
                            while ($a = $areas->fetch_assoc()) {
                                echo '<option value="' . htmlspecialchars($a['descripcion']) . '">' . htmlspecialchars($a['descripcion']) . '</option>';
                            }
                            // Carreras
                            $carreras = $conn->query("SELECT descripcion FROM Carrera ORDER BY descripcion");
                            while ($c = $carreras->fetch_assoc()) {
                                echo '<option value="' . htmlspecialchars($c['descripcion']) . '">' . htmlspecialchars($c['descripcion']) . '</option>';
                            }
                        ?>
                    </select>

                    <select class="filter-select" id="filtroTipo" onchange="buscarMaterial()">
                        <option value="">Todos los tipos</option>
                        <?php
                            $result2 = $conn->query("SELECT DISTINCT tipoMaterial FROM vista_material ORDER BY tipoMaterial");
                            while ($row2 = $result2->fetch_assoc()) {
                                echo '<option value="' . htmlspecialchars($row2['tipoMaterial']) . '">' . htmlspecialchars($row2['tipoMaterial']) . '</option>';
                            }
                        ?>
                    </select>

                    <select class="filter-select" id="filtroEstado" onchange="buscarMaterial()">
                        <option value="">Todos los estados</option>
                        <option value="disponible">Disponibles</option>
                        <option value="nodisponible">Sin disponibles</option>
                    </select>

                    <select class="filter-select" id="filtroOrden" onchange="buscarMaterial()">
                        <option value="titulo">Ordenar: Título A–Z</option>
                        <option value="autor">Autor A–Z</option>
                        <option value="disponibles">Mayor disponibilidad</option>
                        <option value="ejemplares">Más ejemplares</option>
                    </select>
                </div>
            </div>

            <!-- contenedor formulario agregar -->
            <div id="contenedorFormulario"></div>

            <!-- ── Resultados ── -->
            <div style="margin-top:14px;">
                <div class="results-header">
                    <span id="resCount"></span>
                </div>
                <div id="listaResultados"></div>
            </div>

        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script>
// ── Navegación sidebar 
function seleccionarBoton(btn) {
    document.querySelectorAll('.nav-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
}

// ── Chips de filtro 
let campoActivo    = 'todo';
let timeoutBusqueda = null;
let paginaActual   = 1;

function setChip(el) {
    document.querySelectorAll('.chip-filtro').forEach(c => c.classList.remove('active'));
    el.classList.add('active');
    campoActivo = el.dataset.campo;
    const ph = {
        titulo:    'Buscar por título...',
        autor:     'Buscar por autor...',
        isbn:      'Buscar por ISBN...',
        editorial: 'Buscar por editorial...',
        todo:      'Escribe aquí para buscar...'
    };
    document.getElementById('buscador').placeholder = ph[campoActivo] || 'Buscar...';
    buscarMaterial();
}

// ── Búsqueda de material
function buscarMaterial() {
    clearTimeout(timeoutBusqueda);
    timeoutBusqueda = setTimeout(() => _ejecutarBusqueda(1), 300);
}
//recolecta todos los filtros
function _ejecutarBusqueda(pagina = 1) {
    paginaActual = pagina;
    const q              = document.getElementById('buscador').value.trim();
    const clasificacion  = document.getElementById('filtroClasificacion').value;
    const tipo           = document.getElementById('filtroTipo').value;
    const estado         = document.getElementById('filtroEstado').value;
    const orden          = document.getElementById('filtroOrden').value;

    const params = new URLSearchParams({
        q, campo: campoActivo, clasificacion, tipo, estado, orden, pagina
    });
// petición HTTP al servidor en segundo plano sin recargar la página
    fetch('buscar_material.php?' + params)
        .then(r => r.json())
        .then(data => {
            if (data.ok) {
                renderTarjetas(data.materiales);
                renderPaginacion(data.pagina, data.totalPaginas, data.total);
            } else mostrarError('Error al cargar los materiales: ' + data.error);
        })
        .catch(() => mostrarError('Error de conexión con el servidor.'));
}
//buscar_material.php regresa un JSON un formato de datos para dibujar las tarjetas y la paginación sin recargar nada
// Renderizar tarjetas horizontales 
function renderTarjetas(materiales) {
    const cont  = document.getElementById('listaResultados');
    const count = document.getElementById('resCount');

    count.textContent = materiales.length + ' resultado' +
        (materiales.length !== 1 ? 's' : '') + ' encontrado' +
        (materiales.length !== 1 ? 's' : '');

    if (!materiales.length) {
        cont.innerHTML = '<div class="sin-resultados">No se encontraron materiales para tu búsqueda.</div>';
        return;
    }

        // Colores según clasificación (área o carrera)
        //Es un objeto donde cada clasificación tiene tres colores
    const paleta = {
        'Ciencias':      { bg: '#e1f5ee', tc: '#085041', sp: '#1d9e75' },
        'Programacion':  { bg: '#f5f0c0', tc: '#5a5200', sp: '#c8c200' },
        'Programación':  { bg: '#f5f0c0', tc: '#5a5200', sp: '#c8c200' },
        'Economia':      { bg: '#e6f1fb', tc: '#0c447c', sp: '#378add' },
        'Economía':      { bg: '#e6f1fb', tc: '#0c447c', sp: '#378add' },
        'Ingenieria':    { bg: '#eaf3de', tc: '#27500a', sp: '#639922' },
        'Ingeniería':    { bg: '#eaf3de', tc: '#27500a', sp: '#639922' },
        'Sistemas':      { bg: '#faeeda', tc: '#633806', sp: '#ba7517' },
        'Matematicas':   { bg: '#faeeda', tc: '#633806', sp: '#ba7517' },
        'Matemáticas':   { bg: '#faeeda', tc: '#633806', sp: '#ba7517' },
    };
        // Colores por defecto
        const def = { bg: '#f1efe8', tc: '#444441', sp: '#888780' };
        //Para cada libro en la lista decide
        cont.innerHTML = materiales.map(m => {
        const c   = paleta[m.clasificacion] || def;
        const d   = parseInt(m.disponibles     ?? 0);
        const e   = parseInt(m.totalEjemplares ?? 0);

        // Color del número según disponibilidad
        const col = d === 0 ? '#e24b4a' : d <= 1 ? '#ba7517' : '#27500a';
        const cls = d === 0 ? 'badge-rojo' : d <= 1 ? 'badge-amarillo' : 'badge-verde';
        const lbl = d === 0 ? 'Sin disponibles' : d + ' de ' + e + ' disp.';

        // Si no es prestable, mostrar badge gris
        const badgeDisp = m.esPrestable === 'no'
            ? '<span class="hcard-badge badge-gris">Solo consulta</span>'
            : '<span class="hcard-badge ' + cls + '">' + lbl + '</span>';

        const dispNum = m.esPrestable === 'no'
            ? '<div class="hcard-disp-num" style="color:#888;">—</div>'
            : '<div class="hcard-disp-num" style="color:' + col + ';">' + d + '</div>';
        //Columna izquierda — El ícono del libro 
        return `
        
        <div class="hcard" >
            <div class="hcard-icono" style="background:${c.bg};">
                <div class="hcard-spine" style="background:${c.sp};"></div>
                <svg width="20" height="26" viewBox="0 0 20 26" fill="none" stroke="${c.tc}" stroke-width="1.6">
                    <rect x="3" y="1" width="13" height="21" rx="1"/>
                    <path d="M6 1v21"/>
                    <path d="M16 5h2v18H6"/>
                </svg>
            </div>
            <!-- Columna central — La información del libro -->
            <div class="hcard-main">
                <div class="hcard-title">${m.titulo}</div>
                <div class="hcard-sub">${m.autor} · ${m.editorial} · ${m.anioPublicacion ?? ''}</div>
                <div class="hcard-pills">
                    <span class="hpill">${m.tipoMaterial}</span>
                    ${m.clasificacion ? '<span class="hpill">' + m.clasificacion + '</span>' : ''}
                    ${m.isbn          ? '<span class="hpill">ISBN: ' + m.isbn + '</span>'    : ''}
                    ${m.edicion       ? '<span class="hpill">' + m.edicion + ' ed.</span>'  : ''}
                </div>
            </div>
            <div class="hcard-right">
            <!-- Columna derecha — Disponibilidad -->
                <div>
                    ${dispNum}
                    <div class="hcard-disp-lbl">${m.esPrestable === 'no' ? 'no prestable' : 'disponibles'}</div>
                </div>
                ${badgeDisp}
                <button class="hcard-det-btn" onclick="event.stopPropagation(); verDetalle(${m.idMaterial})">Ver detalle</button>
            </div>
        </div>`;
    }).join('');
}


function renderPaginacion(pagina, totalPaginas, total) {
    const count = document.getElementById('resCount');
    count.textContent = total + ' resultado' + (total !== 1 ? 's' : '') + ' encontrado' + (total !== 1 ? 's' : '') + ' — Página ' + pagina + ' de ' + totalPaginas;

    let html = '<div style="display:flex;gap:6px;justify-content:center;margin-top:14px;flex-wrap:wrap;">';

    if (pagina > 1) {
        html += `<button class="btn-pag" onclick="_ejecutarBusqueda(${pagina - 1})">&#8592;  </button>`;
    }

    for (let i = 1; i <= totalPaginas; i++) {
        if (i === pagina) {
            html += `<button class="btn-pag btn-pag-activo">${i}</button>`;
        } else if (i === 1 || i === totalPaginas || (i >= pagina - 2 && i <= pagina + 2)) {
            html += `<button class="btn-pag" onclick="_ejecutarBusqueda(${i})">${i}</button>`;
        } else if (i === pagina - 3 || i === pagina + 3) {
            html += `<span style="align-self:center;color:#aaa;">...</span>`;
        }
    }

    if (pagina < totalPaginas) {
        html += `<button class="btn-pag" onclick="_ejecutarBusqueda(${pagina + 1})" > &#8594;</button>`;
    }

    html += '</div>';
    document.getElementById('listaResultados').insertAdjacentHTML('beforeend', html);
}

function mostrarError(msg) {
    document.getElementById('listaResultados').innerHTML =
        '<div class="sin-resultados">' + msg + '</div>';
}

// ── Ver detalle con SweetAlert2 ──────────────────────────────────
function verDetalle(id) {
    fetch('buscar_material_id.php?id=' + id)
        .then(r => r.json())
        .then(data => {
            if (!data.ok) return;
            const m = data.material;
            const esAdmin = <?= ($_SESSION['tipoUsuario'] === 'Administrador') ? 'true' : 'false' ?>;

            Swal.fire({
                title: m.titulo,
                html: `
                    <table style="width:100%;text-align:left;font-size:13px;border-collapse:collapse;">
                        <tr><td style="color:#888;padding:5px 4px;width:45%;">Autor</td><td><b>${m.autor}</b></td></tr>
                        <tr><td style="color:#888;padding:5px 4px;">Editorial</td><td>${m.editorial}</td></tr>
                        <tr><td style="color:#888;padding:5px 4px;">ISBN</td><td>${m.isbn || '—'}</td></tr>
                        <tr><td style="color:#888;padding:5px 4px;">Tipo</td><td>${m.tipoMaterial}</td></tr>
                        <tr><td style="color:#888;padding:5px 4px;">Clasificación</td><td>${m.clasificacion || '—'}</td></tr>
                        <tr><td style="color:#888;padding:5px 4px;">Edición</td><td>${m.edicion || '—'}</td></tr>
                        <tr><td style="color:#888;padding:5px 4px;">Año publicación</td><td>${m.anioPublicacion || '—'}</td></tr>
                        <tr><td style="color:#888;padding:5px 4px;">Total ejemplares</td><td>${m.totalEjemplares}</td></tr>
                        <tr><td style="color:#888;padding:5px 4px;">Disponibles</td>
                            <td><b style="color:${parseInt(m.disponibles) > 0 ? '#27500a' : '#e24b4a'}">${m.disponibles}</b></td></tr>
                        <tr><td style="color:#888;padding:5px 4px;">Prestable</td>
                            <td><b>${m.esPrestable === 'si' ? 'Sí' : 'No'}</b></td></tr>
                    </table>`,
                confirmButtonColor: '#198754',
                confirmButtonText: 'Cerrar',
                showDenyButton: esAdmin,
                denyButtonText: 'Dar de baja',
                denyButtonColor: '#cd5e5e'
            }).then(result => {
                if (result.isDenied) {
                    darDeBaja(id);
                }
            });
        });
}

function darDeBaja(id) {
    Swal.fire({
        title: '¿Dar de baja?',
        text: 'Todos los ejemplares quedarán como baja y no podrán prestarse. El historial se conserva.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#a32d2d',
        cancelButtonColor: '#aaa',
        confirmButtonText: 'Sí, dar de baja',
        cancelButtonText: 'Cancelar'
    }).then(result => {
        if (result.isConfirmed) {
            const fd = new FormData();
            fd.append('idMaterial', id);
            fetch('baja_material.php', { method: 'POST', body: fd })
                .then(r => r.json())
                .then(data => {
                    if (data.ok) {
                        Swal.fire({ icon: 'success', title: '¡Dado de baja!', text: data.mensaje, confirmButtonColor: '#198754' })
                        .then(() => _ejecutarBusqueda(paginaActual));
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error', text: data.error, confirmButtonColor: '#dc3545' });
                    }
                });
        }
    });
}

// ── Formulario agregar material ──────────────────────────────────
const reglas = {
    'libro'      : { isbn: true,  area: true,  carrera: false, prestable: true  },
    'revista'    : { isbn: false, area: true,  carrera: false, prestable: true  },
    'tesis'      : { isbn: false, area: false, carrera: true,  prestable: false },
    'residencia' : { isbn: false, area: false, carrera: true,  prestable: false },
    'multimedia' : { isbn: false, area: false, carrera: false, prestable: false }
};

function actualizarCampos() {
    const select = document.getElementById('tipoMaterial');
    const tipo   = select?.options[select.selectedIndex]?.dataset.tipo || '';
    const r      = reglas[tipo] || { isbn: false, area: false, carrera: false, prestable: false };
    document.getElementById('campoISBN').style.display           = r.isbn      ? '' : 'none';
    document.getElementById('campoArea').style.display           = r.area      ? '' : 'none';
    document.getElementById('campoCarrera').style.display        = r.carrera   ? '' : 'none';
    document.getElementById('campoPrestable').style.display      = r.prestable ? '' : 'none';
    document.getElementById('campoEjemplaresSolo').style.display = !r.prestable && tipo !== '' ? '' : 'none';
    if (document.getElementById('selectArea'))      document.getElementById('selectArea').required      = r.area;
    if (document.getElementById('selectCarrera'))   document.getElementById('selectCarrera').required   = r.carrera;
    if (document.getElementById('inputEjemplares')) document.getElementById('inputEjemplares').required = r.prestable;
}

function cargarFormulario() {
    fetch('agregar_material.php')
        .then(res => res.text())
        .then(html => {
            document.getElementById('contenedorFormulario').innerHTML = html;
            document.getElementById('formAgregar').scrollIntoView({ behavior: 'smooth' });
        });
}

function cerrarFormulario() {
    document.getElementById('contenedorFormulario').innerHTML = '';
}

// ── Cargar todos los materiales al abrir la página ───────────────
document.addEventListener('DOMContentLoaded', () => _ejecutarBusqueda());

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

function validarFormulario() {
    const titulo = document.querySelector('[name="titulo"]').value.trim();
    const autor  = document.querySelector('[name="autor"]').value.trim();
    const soloLetras = /^[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ\s.,'-]+$/;

    if (!titulo) {
        Swal.fire({ icon: 'warning', title: 'Campo requerido', text: 'El título es obligatorio.', confirmButtonColor: '#b8b800' });
        return;
    }
    if (!autor) {
        Swal.fire({ icon: 'warning', title: 'Campo requerido', text: 'El autor es obligatorio.', confirmButtonColor: '#b8b800' });
        return;
    }
    if (!soloLetras.test(autor)) {
        Swal.fire({ icon: 'warning', title: 'Autor inválido', text: 'El autor solo debe contener letras.', confirmButtonColor: '#b8b800' });
        return;
    }
    if (!soloLetras.test(titulo)) {
        Swal.fire({ icon: 'warning', title: 'Título inválido', text: 'El título solo debe contener letras.', confirmButtonColor: '#b8b800' });
        return;
    }

    document.querySelector('form').submit();
}

</script>


</body>
</html>