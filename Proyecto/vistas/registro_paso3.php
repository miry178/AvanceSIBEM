<?php
session_start();

// Si no pasó por la verificación, regresar al paso 1
if(!isset($_SESSION['correo_verificado'])){
    header("Location: ../registro_paso1.php?error=4");
    exit();
}

$error = $_GET['error'] ?? '';
if($error === '1'){
    echo '<div class="alert alert-danger text-center" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>Las contraseñas no coinciden. Intenta de nuevo.
          </div>';
}
if($error === '2'){
    echo '<div class="alert alert-warning text-center" role="alert">
            <i class="bi bi-exclamation-circle-fill me-2"></i>La contraseña debe tener al menos 6 caracteres.
          </div>';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIBEM - Crear Contraseña</title>

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
                        <?php $esRecuperacion = $_SESSION['es_recuperacion'] ?? false; ?>
                        <h5 class="text-center fw-bold mb-1">
                            <?= $esRecuperacion ? 'Nueva contraseña' : 'Crea tu contraseña' ?>
                        </h5>
                        <p class="text-center text-muted small mb-4">
                            <?= $esRecuperacion 
                                ? 'Ingresa tu nueva contraseña para recuperar el acceso a tu cuenta SIBEM.' 
                                : 'Ya verificamos tu identidad. Ahora crea una contraseña para tu cuenta SIBEM.' ?>
                        </p>

                        <!-- Paso 3: crear la contraseña -->
                        <form action="../php/php_registro/guardar_password.php" method="POST">

                            <!-- Campo nueva contraseña -->
                            <div class="mb-3">
                                <label for="password" class="form-label">Nueva contraseña</label>
                                <div class="input-group">
                                    <input type="password"
                                           class="form-control input-sibem"
                                           id="password"
                                           name="password"
                                           placeholder="Mínimo 6 caracteres"
                                           required
                                           minlength="6">
                                    <button class="input-group-text" type="button" id="togglePass1">
                                        <i class="bi bi-eye-fill text-secondary" id="iconoOjo1"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Campo confirmar contraseña -->
                            <div class="mb-3">
                                <label for="confirmar" class="form-label">Confirmar contraseña</label>
                                <div class="input-group">
                                    <input type="password"
                                           class="form-control input-sibem"
                                           id="confirmar"
                                           name="confirmar"
                                           placeholder="Repite tu contraseña"
                                           required>
                                    <button class="input-group-text" type="button" id="togglePass2">
                                        <i class="bi bi-eye-fill text-secondary" id="iconoOjo2"></i>
                                    </button>
                                </div>
                                <!-- Mensaje de validación en tiempo real -->
                                <div id="mensajeCoincide" class="form-text small"></div>
                            </div>

                            <div class="d-flex justify-content-end mb-3">
                                <button type="submit" class="btn btn-login px-4" id="btnGuardar">
                                    Guardar contraseña
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Mostrar/ocultar contraseña campo 1
        document.getElementById('togglePass1').addEventListener('click', function(){
            const campo = document.getElementById('password');
            const icono = document.getElementById('iconoOjo1');
            if(campo.type === 'password'){
                campo.type = 'text';
                icono.classList.replace('bi-eye-fill', 'bi-eye-slash-fill');
            } else {
                campo.type = 'password';
                icono.classList.replace('bi-eye-slash-fill', 'bi-eye-fill');
            }
        });

        // Mostrar/ocultar contraseña campo 2
        document.getElementById('togglePass2').addEventListener('click', function(){
            const campo = document.getElementById('confirmar');
            const icono = document.getElementById('iconoOjo2');
            if(campo.type === 'password'){
                campo.type = 'text';
                icono.classList.replace('bi-eye-fill', 'bi-eye-slash-fill');
            } else {
                campo.type = 'password';
                icono.classList.replace('bi-eye-slash-fill', 'bi-eye-fill');
            }
        });

        // Validación en tiempo real: verificar que las contraseñas coincidan
        document.getElementById('confirmar').addEventListener('input', function(){
            const pass1 = document.getElementById('password').value;
            const pass2 = this.value;
            const mensaje = document.getElementById('mensajeCoincide');
            const boton  = document.getElementById('btnGuardar');

            if(pass2 === ''){
                mensaje.textContent = '';
            } else if(pass1 === pass2){
                mensaje.innerHTML = '<i class="bi bi-check-circle-fill text-success"></i> Las contraseñas coinciden';
                mensaje.style.color = '#2e7d32';
                boton.disabled = false;
            } else {
                mensaje.innerHTML = '<i class="bi bi-x-circle-fill text-danger"></i> Las contraseñas no coinciden';
                mensaje.style.color = '#c62828';
                boton.disabled = true;
            }
        });
    </script>

</body>
</html>
