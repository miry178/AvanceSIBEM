<?php
session_start();

// Verificar que exista una sesión activa con el correo
if(!isset($_SESSION['correo_verificacion'])){
    header("Location: ../../vistas/registro_paso1.php");
    exit();
}

// Recibir el código que escribió el usuario
$codigoIngresado = trim($_POST['codigo'] ?? '');

// ---- 1. Verificar que el código no haya expirado ----
if(time() > $_SESSION['codigo_expiracion']){
    unset($_SESSION['codigo_verificacion']);
    unset($_SESSION['codigo_expiracion']);
    header("Location: ../../vistas/registro_paso2.php?error=2");
    exit();
}

// ---- 2. Verificar que el código sea correcto ----
if((string)$codigoIngresado !== (string)$_SESSION['codigo_verificacion']){
    header("Location: ../../vistas/registro_paso2.php?error=1");
    exit();
}

// ---- 3. Código correcto ----
$_SESSION['correo_verificado'] = $_SESSION['correo_verificacion'];
unset($_SESSION['correo_verificacion']);
unset($_SESSION['codigo_verificacion']);
unset($_SESSION['codigo_expiracion']);

// Ir al paso 3: crear contraseña
header("Location: ../../vistas/registro_paso3.php");
exit();
?>