<?php
session_start();

// Incluimos la conexión a la BD
require_once '../../bd/conexion.php';
// Incluimos PHPMailer — estos 3 archivos vienen de la carpeta src/ que descargaste
require_once '../mailer/src/Exception.php';
require_once '../mailer/src/PHPMailer.php';
require_once '../mailer/src/SMTP.php';

// Recibimos el correo del formulario (método POST)
$correo = trim($_POST['correo'] ?? '');

// ---- 1. Verificar que el correo exista en la BD ----
$stmt = $conn->prepare("SELECT idUsuario, nombre, password FROM Usuario WHERE correoInst = ?");
$stmt->bind_param("s", $correo);
$stmt->execute();
$resultado = $stmt->get_result();

if($resultado->num_rows === 0){
    // El correo NO existe en la BD
    header("Location: ../../vistas/registro_paso1.php?error=1");
    exit();
}

$usuario = $resultado->fetch_assoc();

// ---- 2. Verificar si ya tiene password (ya está registrado) ----
if(!empty($usuario['password'])){
    // Ya tiene password, no necesita registrarse de nuevo
    header("Location: ../../vistas/registro_paso1.php?error=2");
    
    exit();
}

// ---- 3. Generar código de 6 dígitos aleatorio ----
// rand(100000, 999999) genera un número entre 100000 y 999999
$codigo = rand(100000, 999999);

// ---- 4. Guardar el código y su expiración en la sesión ----
// La sesión guarda datos entre páginas sin pasarlos por URL
$_SESSION['correo_verificacion'] = $correo;
$_SESSION['codigo_verificacion'] = $codigo;
$_SESSION['codigo_expiracion']   = time() + (10 * 60); // expira en 10 minutos
// time() devuelve el tiempo actual en segundos, le sumamos 600 segundos (10 min)

// ---- 5. Enviar el correo con PHPMailer ----
$mail = new PHPMailer\PHPMailer\PHPMailer(true);
// El parámetro true activa el manejo de excepciones (errores)

try {
    // Configuración del servidor SMTP de Gmail usando sockets
$mail->isSMTP();
$mail->Host       = 'smtp.gmail.com';
$mail->SMTPAuth   = true;
$mail->Username   = 'sibembiblioteca@gmail.com';
$mail->Password   = 'yblu wpif sglj mmnh';
$mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
$mail->Port       = 465;
$mail->SMTPOptions = array(
    'ssl' => array(
        'verify_peer'       => false,
        'verify_peer_name'  => false,
        'allow_self_signed' => true
    )
);

    // Configuración del correo
    $mail->setFrom('sibembiblioteca@gmail.com', 'SIBEM Biblioteca');
    $mail->addAddress($correo, $usuario['nombre']); // Destinatario

    $mail->CharSet = 'UTF-8'; // Para que los acentos se vean bien

    // Asunto y cuerpo del correo
    $mail->Subject = 'Código de verificación SIBEM';
    $mail->isHTML(true); // Activar HTML en el correo
    $mail->Body = "
        <div style='font-family: Arial, sans-serif; max-width: 500px; margin: auto;'>
            <h2 style='color: #1e4d2b;'>SIBEM - Sistema de Biblioteca</h2>
            <p>Hola <strong>{$usuario['nombre']}</strong>,</p>
            <p>Tu código de verificación para crear tu contraseña es:</p>
            <div style='background: #e8f5e9; border: 1px solid #a5d6a7;
                        padding: 20px; text-align: center; border-radius: 8px;
                        font-size: 32px; font-weight: bold; letter-spacing: 8px;
                        color: #1b5e20;'>
                {$codigo}
            </div>
            <p style='color: #555; margin-top: 15px;'>
                Este código expira en <strong>10 minutos</strong>.<br>
                Si no solicitaste este código, ignora este correo.
            </p>
            <hr style='border-color: #c8e6c9;'>
            <p style='color: #888; font-size: 12px;'>
                ITS Ciudad Constitución — Sistema SIBEM
            </p>
        </div>
    ";

    $mail->send();

    // Si el correo se envió bien, ir al paso 2
    header("Location: ../../vistas/registro_paso2.php");
    exit();

} catch (Exception $e) {
    // Mostrar el error real para depurar
    die("Error al enviar: " . $mail->ErrorInfo);
}
?>