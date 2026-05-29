<?php
session_start();

// Verificar que llegó aquí después de verificar el correo
if(!isset($_SESSION['correo_verificado'])){
    header("Location: ../../vistas/registro_paso1.php");
    exit();
}

// Incluir conexión a BD
require_once '../../bd/conexion.php';

// Recibir las contraseñas del formulario
$password  = $_POST['password']  ?? '';
$confirmar = $_POST['confirmar'] ?? '';

// ---- 1. Validar que la contraseña tenga mínimo 6 caracteres ----
if(strlen($password) < 6){
    header("Location: ../../vistas/registro_paso3.php?error=2");
    exit();
}

// ---- 2. Validar que ambas contraseñas coincidan ----
if($password !== $confirmar){
    header("Location: ../../vistas/registro_paso3.php?error=1");
    exit();
}

// ---- 3. Encriptar la contraseña con password_hash ----
$passwordEncriptada = password_hash($password, PASSWORD_DEFAULT);

// ---- 4. Guardar la contraseña en la BD ----
$correo = $_SESSION['correo_verificado'];

$stmt = $conn->prepare("UPDATE Usuario SET password = ? WHERE correoInst = ?");
$stmt->bind_param("ss", $passwordEncriptada, $correo);
$stmt->execute();
if(!$stmt->error){
    $esRecuperacion = $_SESSION['es_recuperacion'] ?? false;
    unset($_SESSION['correo_verificado']);
    unset($_SESSION['es_recuperacion']);
    if($esRecuperacion){
        header("Location: ../../index.php?exito=1");
    } else {
        header("Location: ../../index.php?exito=1");
    }
    exit();
}
?>
