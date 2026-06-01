<?php
session_start();
if (isset($_SESSION['idUsuario']) && isset($_SESSION['nombre']) && isset($_SESSION['tipoUsuario'])) {
    header("Location: administrador/home/inicio.php");
    exit();
}
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

$error = $_GET['error'] ?? '';
$exito = $_GET["exito"] ?? "";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIBEM - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="login/diseno_login.css">
</head>
<body class="d-flex align-items-center justify-content-center">

<main class="container py-4">

    <?php if($error === '1'): ?>
    <div class="alert alert-danger text-center" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>Error: usuario o contraseña incorrectos.
    </div>
    <?php elseif($error === '2'): ?>
    <div class="alert alert-warning text-center" role="alert">
        <i class="bi bi-exclamation-circle-fill me-2"></i>Error: no iniciaste sesión.
    </div>
    <?php elseif($error === '3'): ?>
    <div class="alert alert-warning text-center" role="alert">
        <i class="bi bi-shield-exclamation me-2"></i>Error: no tienes permisos para acceder a esta página.
    </div>
    <?php elseif($error === '4'): ?>
    <div class="alert alert-warning text-center" role="alert">
        <i class="bi bi-person-x-fill me-2"></i>Tu cuenta está desactivada. Contacta al administrador.
    </div>
    <?php elseif($exito === '1'): ?>
    <div class="alert alert-success text-center" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>¡Contraseña creada exitosamente! Ya puedes iniciar sesión.
    </div>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-9 col-xl-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="row align-items-center g-3">

                        <div class="col-12 col-md-6" >
                        <img src="/ProyectoBiblioteca/Proyecto/administrador/img/biblioteca.png" alt="Biblioteca" 
                            style="width:100%; height:250px; object-fit:cover; border-radius:8px;">
                                
                    </div>

                        <div class="col-12 col-md-6 px-md-4">
                            <h4 class="text-center fw-bold mb-3">Welcome back to SIBEM</h4>
                            <div class="text-center mb-4">
                                <img src="administrador/img/Logo.png" alt="Logo ITS Ciudad Constitución"
                                     style="max-height: 80px;" onerror="this.style.display='none'">
                            </div>
                            <form action="php/php_login/login.php" method="POST">
                                <div class="mb-3">
                                    <div class="input-group">
                                        <input type="email" class="form-control input-sibem"
                                               id="email" name="email" placeholder="Email" required>
                                        <span class="input-group-text">
                                            <i class="bi bi-envelope-fill text-secondary"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <div class="input-group">
                                        <input type="password" class="form-control input-sibem"
                                               id="password" name="password" placeholder="Password" required>
                                        <button class="input-group-text border-0" type="button" id="togglePassword">
                                            <i class="bi bi-eye-fill text-secondary" id="iconoOjo"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <a href="vistas/registro_paso1.php" class="link-olvido">¿Olvidó su contraseña?</a>
                                </div>
                                <div class="d-flex justify-content-end mb-3">
                                    <button type="submit" class="btn btn-login px-4">Login</button>
                                </div>
                                <div class="text-center">
                                    <a href="vistas/registro_paso1.php" class="text-dark fw-semibold text-decoration-none">
                                        Registrarse
                                    </a>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
const togglePassword = document.getElementById('togglePassword');
const campoPassword  = document.getElementById('password');
const iconoOjo       = document.getElementById('iconoOjo');
togglePassword.addEventListener('click', function () {
    if (campoPassword.type === 'password') {
        campoPassword.type = 'text';
        iconoOjo.classList.replace('bi-eye-fill', 'bi-eye-slash-fill');
    } else {
        campoPassword.type = 'password';
        iconoOjo.classList.replace('bi-eye-slash-fill', 'bi-eye-fill');
    }
});
</script>
</body>
</html>