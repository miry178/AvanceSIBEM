<?php
// Iniciamos la sesión para poder guardar datos del usuario
session_start();

// Conectamos a la base de datos
require_once '../../bd/conexion.php';

// Recibimos el correo y contraseña que el usuario escribió en el formulario
$correo   = trim($_POST['email']    ?? '');
$password = $_POST['password'] ?? '';

// ---- 1. Verificar que el correo exista en la BD ----
// Buscamos en la tabla Usuario si existe ese correo institucional
$stmt = $conn->prepare("SELECT idUsuario, nombre, password, activo FROM Usuario WHERE correoInst = ?");
$stmt->bind_param("s", $correo);
$stmt->execute();
$resultado = $stmt->get_result();

// Si no encontró ningún usuario con ese correo, regresa al login con error
if($resultado->num_rows === 0){
    header("Location: ../../index.php?error=1");
    exit();
}

// Guardamos los datos del usuario encontrado en un arreglo
$usuario = $resultado->fetch_assoc();

// ---- 2. Verificar si la cuenta está activa ----
// Si el administrador desactivó esta cuenta, no puede ingresar
if($usuario['activo'] === 'no'){
    header("Location: ../../index.php?error=4");
    exit();
}

// ---- 3. Verificar si ya tiene contraseña ----
// Si el campo password está vacío, el usuario nunca se ha registrado
// Lo mandamos al paso 1 del registro para que cree su contraseña

if(empty($usuario['password'])){
    header("Location: ../../vistas/registro_paso1.php?error=3");
    exit();
}
// ---- 4. Verificar la contraseña ----
// password_verify compara la contraseña ingresada contra el hash guardado en BD
// trim() elimina espacios en blanco al inicio y al final por si acaso

if(!password_verify(trim($password), trim($usuario['password']))){
    header("Location: ../../index.php?error=1");
    exit();
}
// ---- 5. Login exitoso — guardar datos en sesión ----
// Guardamos los datos del usuario en la sesión para que otras páginas sepan quién está logueado
$_SESSION['idUsuario'] = $usuario['idUsuario'];
$_SESSION['nombre']    = $usuario['nombre'];
$_SESSION['correo']    = $correo;

// ---- 6. Obtener el tipo de persona ----
// Consultamos qué tipo de usuario es (Administrador, Alumno, Docente, etc.)
// para saber a qué página redirigirlo
$stmtTipo = $conn->prepare("
    SELECT r.descripcion 
    FROM RelRol rr
    JOIN Rol r ON rr.idRol = r.idRol
    WHERE rr.idUsuario = ?
    LIMIT 1
");
$stmtTipo->bind_param("s", $usuario['idUsuario']);
$stmtTipo->execute();
$resTipo = $stmtTipo->get_result();
$tipo    = $resTipo->fetch_assoc();
// Guardamos el tipo en la sesión, si no tiene tipo asignado se pone Invitado por defecto
$_SESSION['tipoUsuario'] = $tipo['descripcion'] ?? 'Invitado';

// ---- 7. Redirigir según tipo de usuario ----
// Según el rol del usuario lo mandamos a la interfaz correspondiente
switch($_SESSION['tipoUsuario']){
    case 'Administrador':
    case 'Encargado':
        header("Location: ../../administrador/home/inicio.php");
        break;
    case 'Alumno':
    case 'Docente':
    case 'Personal':
    case 'Invitado':
    default:
        header("Location: ../../administrador/home/inicio.php");
        break;
}
exit();
?>