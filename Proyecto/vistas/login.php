<?php
$error = $_GET['error'] ?? '';
$exito = $_GET['exito'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIBEM - Login</title>

    <!-- Bootstrap 5.3.8 CSS — igual que el ejemplo de clase -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet"
          integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
          crossorigin="anonymous">

    <!-- Bootstrap Icons — librería oficial de íconos de Bootstrap (para el ojo y el correo) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
          rel="stylesheet">

    <!-- Bootstrap 5.3.8 JS — igual que el ejemplo de clase -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
            crossorigin="anonymous"></script>

    <!-- CSS propio del login — separado en su archivo para mejor organización -->
    <link rel="stylesheet" href="../login/diseno_login.css">
</head>

<body class="d-flex align-items-center justify-content-center">

    <!-- Contenedor principal, centrado vertical y horizontal -->
    <main class="container py-4">
        <div class="row justify-content-center">

            <!-- Card principal blanca — col adaptable a distintos dispositivos -->
            <div class="col-12 col-md-10 col-lg-9 col-xl-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">

                        <!-- 
                            ROW INTERNA: dos columnas
                            col-12 = en móvil ocupa todo el ancho (se apilan)
                            col-md-6 = en pantallas medianas y mayores, 50% cada una
                        -->
                        <div class="row align-items-center g-3">

                            <!-- COLUMNA IZQUIERDA: panel decorativo verde claro -->
                            <div class="col-12 col-md-6">
                                <div class="panel-decorativo d-flex align-items-center justify-content-center">
                                    <!--
                                        Aquí puedes poner una imagen de tu biblioteca,
                                        por ahora solo es el panel de color como en la referencia.
                                        Ejemplo: <img src="img/biblioteca.jpg" class="img-fluid rounded" alt="Biblioteca">
                                    -->
                                    <p class="text-muted small text-center px-3">
                                        <i class="bi bi-image fs-1 d-block mb-2 text-success opacity-50"></i>
                                        Imagen decorativa
                                    </p>
                                </div>
                            </div>

                            <!-- COLUMNA DERECHA: formulario de login -->
                            <div class="col-12 col-md-6 px-md-4">

                                <!-- Título -->
                                <h4 class="text-center fw-bold mb-3">Welcome back to SIBEM</h4>

                                <!-- Mensajes de error/éxito -->
                                <?php
                                $error = $_GET['error'] ?? '';
                                $exito = $_GET['exito'] ?? '';
                                if($exito === '1'): ?>
                                    <div class="alert alert-success text-center">¡Contraseña creada! Ya puedes iniciar sesión.</div>
                                <?php elseif($error === '1'): ?>
                                    <div class="alert alert-danger text-center">Correo o contraseña incorrectos.</div>
                                <?php elseif($error === '4'): ?>
                                    <div class="alert alert-warning text-center">Tu cuenta está desactivada.</div>
                                <?php endif; ?>

                                <!-- 
                                    Logo del ITS — ruta relativa a donde tengas el archivo.
                                    Cámbia "img/logo_its.png" por la ruta real de tu logo.
                                -->
                                <div class="text-center mb-4">
                                    <img src="img/logo_its.png"
                                         alt="Logo ITS Ciudad Constitución"
                                         style="max-height: 80px;"
                                         onerror="this.style.display='none'">
                                </div>

                                <!-- Formulario — action apunta al PHP que procesa el login -->
                                <form action="../php/php_login/login.php" method="POST">
                                

                                    <!-- Campo Email con ícono -->
                                    <div class="mb-3">
                                        <div class="input-group">
                                            <input type="email"
                                                   class="form-control input-sibem"
                                                   id="email"
                                                   name="email"
                                                   placeholder="Email"
                                                   required>
                                            <!-- 
                                                input-group-text: componente de Bootstrap
                                                para poner algo pegado al lado del input 
                                            -->
                                            <span class="input-group-text">
                                                <i class="bi bi-envelope-fill text-secondary"></i>
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Campo Password con ícono de ojo (mostrar/ocultar) -->
                                    <div class="mb-2">
                                        <div class="input-group">
                                            <input type="password"
                                                   class="form-control input-sibem"
                                                   id="password"
                                                   name="password"
                                                   placeholder="Password"
                                                   required>
                                            <!-- 
                                                El botón con id="togglePassword" llama a la función
                                                de abajo en el <script> para mostrar/ocultar la contraseña
                                            -->
                                            <button class="input-group-text border-0"
                                                    type="button"
                                                    id="togglePassword"
                                                    style="cursor: pointer;">
                                                <i class="bi bi-eye-fill text-secondary" id="iconoOjo"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Link olvidó contraseña -->
                                    <div class="mb-3">
                                        <a href="recuperar.php" class="link-olvido">¿Olvidó su contraseña?</a>
                                    </div>

                                    <!-- Botón Login alineado a la derecha como en la referencia -->
                                    <div class="d-flex justify-content-end mb-3">
                                        <button type="submit" class="btn btn-login px-4">
                                            Login
                                        </button>
                                    </div>

                                    <!-- Link Registrarse centrado -->
                                    <div class="text-center">
                                        <a href="registro.php" class="text-dark fw-semibold text-decoration-none">
                                            Registrarse
                                        </a>
                                    </div>

                                </form>
                            </div>
                            <!-- fin columna derecha -->

                        </div>
                        <!-- fin row interna -->

                    </div>
                </div>
            </div>

        </div>
    </main>

    <script>
        // Función para mostrar u ocultar la contraseña al presionar el ícono del ojo
        // getElementById: obtiene el elemento HTML por su id
        const togglePassword = document.getElementById('togglePassword');
        const campoPassword  = document.getElementById('password');
        const iconoOjo       = document.getElementById('iconoOjo');

        togglePassword.addEventListener('click', function () {
            // Si el tipo es 'password' lo cambia a 'text' (visible) y viceversa
            if (campoPassword.type === 'password') {
                campoPassword.type = 'text';
                iconoOjo.classList.replace('bi-eye-fill', 'bi-eye-slash-fill'); // cambia el ícono
            } else {
                campoPassword.type = 'password';
                iconoOjo.classList.replace('bi-eye-slash-fill', 'bi-eye-fill'); // regresa el ícono
            }
        });
    </script>

</body>
</html>
