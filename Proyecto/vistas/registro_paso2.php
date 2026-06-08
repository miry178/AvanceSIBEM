<?php
session_start();

if(!isset($_SESSION['correo_verificacion'])){
    header("Location: ../registro_paso1.php?error=4");
    exit();
}

$error = $_GET['error'] ?? '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIBEM - Verificar Código</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet"
          integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
          crossorigin="anonymous">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
          rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
            crossorigin="anonymous"></script>

    <link rel="stylesheet" href="../login/diseno_login.css">
</head>

<body class="d-flex align-items-center justify-content-center">

    <main class="container py-4">

        <?php if($error === '1'): ?>
        <div class="alert alert-danger text-center" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>Código incorrecto. Intenta de nuevo.
        </div>
        <?php elseif($error === '2'): ?>
        <div class="alert alert-warning text-center" role="alert">
            <i class="bi bi-clock-fill me-2"></i>El código ha expirado. Solicita uno nuevo.
        </div>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-md-5">

                        <!-- Logo -->
                        <div class="text-center mb-3">
                            <img src="img/logo_its.png" alt="Logo ITS" style="max-height: 70px;" onerror="this.style.display='none'">
                        </div>

                        <h5 class="text-center fw-bold mb-1">Verifica tu correo</h5>
                        <p class="text-center text-muted small mb-4">
                            Te enviamos un código de 6 dígitos a tu correo institucional. 
                            Revisa tu bandeja de entrada e ingrésalo aquí.
                            <br><strong>El código expira en 10 minutos.</strong>
                        </p>

                        <!-- Paso 2: ingresar el código recibido por correo -->
                        <form action="../php/php_registro/verificar_codigo.php" method="POST">

                            <div class="mb-3">
                                <label for="codigo" class="form-label">Código de verificación</label>
                                <div class="input-group">
                                    <input type="text"
                                           class="form-control input-sibem text-center fs-5 fw-bold"
                                           id="codigo"
                                           name="codigo"
                                           placeholder="_ _ _ _ _ _"
                                           maxlength="6"
                                           required
                                           autocomplete="off">
                                    <span class="input-group-text">
                                        <i class="bi bi-shield-lock-fill text-secondary"></i>
                                    </span>
                                </div>
                                <div class="form-text text-muted small">Solo números, 6 dígitos.</div>
                            </div>

                            <div class="d-flex justify-content-end mb-3">
                                <button type="submit" class="btn btn-login px-4">
                                    Verificar código
                                </button>
                            </div>

                            <!-- Link para reenviar el código -->
                            <div class="text-center mb-2">
                                <a href="registro_paso1.php" class="link-olvido">
                                    <i class="bi bi-arrow-repeat me-1"></i>Reenviar código
                                </a>
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

    <script>
        // Solo permite escribir números en el campo de código
        document.getElementById('codigo').addEventListener('input', function(){
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    </script>

</body>
</html>