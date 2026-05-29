<?php
$error = $_GET['error'] ?? '';
if($error === '1'){
    echo '<div class="alert alert-danger text-center" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>Error: usuario o contraseña incorrectos.
          </div>';
}
if($error === '2'){
    echo '<div class="alert alert-warning text-center" role="alert">
            <i class="bi bi-exclamation-circle-fill me-2"></i>Error: no iniciaste sesión.
          </div>';
}
if($error === '3'){
    echo '<div class="alert alert-warning text-center" role="alert">
            <i class="bi bi-shield-exclamation me-2"></i>Error: no tienes permisos para acceder a esta página.
          </div>';
}
if($error === '4'){
    echo '<div class="alert alert-warning text-center" role="alert">
            <i class="bi bi-person-x-fill me-2"></i>Tu cuenta está desactivada. Contacta al administrador.
          </div>';
}
$exito = $_GET["exito"] ?? "";
if($exito === "1"){
    echo "<div class='alert alert-success text-center' role='alert'>
            <i class='bi bi-check-circle-fill me-2'></i>¡Contraseña creada exitosamente! Ya puedes iniciar sesión.
          </div>";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIBEM - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet"
          integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
          crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
          rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
            crossorigin="anonymous"></script>
    <link rel="stylesheet" href="login/diseno_login.css">
</head>

<body class="d-flex align-items-center justify-content-center">

<main class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-9 col-xl-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="row align-items-center g-3">

                        <!-- COLUMNA IZQUIERDA -->
                        <div class="col-12 col-md-6">
                            <div class="panel-decorativo d-flex align-items-center justify-content-center">
                                <p class="text-muted small text-center px-3">
                                    <i class="bi bi-image fs-1 d-block mb-2 text-success opacity-50"></i>
                                    Imagen decorativa
                                </p>
                            </div>
                        </div>

                        <!-- COLUMNA DERECHA -->
                        <div class="col-12 col-md-6 px-md-4">
                            <h4 class="text-center fw-bold mb-3">Welcome back to SIBEM</h4>

                            <div class="text-center mb-4">
                                <img src="administrador/img/Logo.png"
                                     alt="Logo ITS Ciudad Constitución"
                                     style="max-height: 80px;"
                                     onerror="this.style.display='none'">
                            </div>
                             <!-- Cuando se presiona login los datos se envian al php/php_login/login.php -->
                            <form action="php/php_login/login.php" method="POST">

                                <div class="mb-3">
                                    <div class="input-group">
                                        <input type="email"
                                               class="form-control input-sibem"
                                               id="email" name="email"
                                               placeholder="Email" required>
                                        <span class="input-group-text">
                                            <i class="bi bi-envelope-fill text-secondary"></i>
                                        </span>
                                    </div>
                                </div>

                                <div class="mb-2">
                                    <div class="input-group">
                                        <input type="password"
                                               class="form-control input-sibem"
                                               id="password" name="password"
                                               placeholder="Password" required>
                                        <button class="input-group-text border-0"
                                                type="button" id="togglePassword"
                                                style="cursor: pointer;">
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
                                    <a href="vistas/registro_paso1.php"
                                       class="text-dark fw-semibold text-decoration-none">
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
 <!-- JavaScript oara mostrar o ocultar el password -->
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