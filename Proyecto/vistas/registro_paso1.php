<?php
// Mensajes de error por parámetro GET
$error = $_GET['error'] ?? '';
if($error === '1'){
    echo '<div class="alert alert-danger text-center" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>Este correo no está registrado en el sistema.
          </div>';
}
if($error === '2'){
    echo '<div class="alert alert-warning text-center" role="alert">
            <i class="bi bi-exclamation-circle-fill me-2"></i>Este correo ya tiene una cuenta activa. Inicia sesión.
          </div>';
}
if($error === '3'){
    echo '<div class="alert alert-danger text-center" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>Hubo un error al enviar el correo. Intenta de nuevo.
          </div>';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIBEM - Registro</title>

    <!-- Bootstrap 5.3.8 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet"
          integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
          crossorigin="anonymous">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
          rel="stylesheet">

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
            crossorigin="anonymous"></script>

    <!-- CSS propio -->
    <link rel="stylesheet" href="../login/diseno_login.css">
</head>

<body class="d-flex align-items-center justify-content-center">

    <main class="container py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-md-5">

                        <!-- Logo -->
                        <div class="text-center mb-3">
                            <img src="img/logo_its.png"
                                 alt="Logo ITS"
                                 style="max-height: 70px;"
                                 onerror="this.style.display='none'">
                        </div>

                        <h5 class="text-center fw-bold mb-1">Registro SIBEM</h5>
                        <p class="text-center text-muted small mb-4">
                            Ingresa tu correo institucional para verificar que estás registrado en el sistema.
                        </p>

                        <!-- Paso 1: solo pide el correo institucional -->
                         <!--  Vista donde el usuario escribe su correo institucional y presiona "Enviar código". -->
                        <form action="../php/php_registro/enviar_codigo.php" method="POST">

                            <div class="mb-3">
                                <label for="correo" class="form-label">Correo institucional:*</label>
                                <div class="input-group">
                                    <input type="email"
                                           class="form-control input-sibem"
                                           id="correo"
                                           name="correo"
                                           placeholder="ejemplo@cdconstitucion.tecnm.mx"
                                           required>
                                    <span class="input-group-text">
                                        <i class="bi bi-envelope-fill text-secondary"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mb-3">
                                <button type="submit" class="btn btn-login px-4">
                                    Enviar código
                                </button>
                            </div>

                            <!-- Link para regresar al login -->
                            <div class="text-center">
                                <a href="../index.php" class="link-olvido">
                                    <i class="bi bi-arrow-left me-1"></i>Regresar al login
                                </a>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

</body>
</html>
