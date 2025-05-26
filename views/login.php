<?php
session_start();

// Si ya hay sesión iniciada, redirigir según rol
if (isset($_SESSION['usuario']) && isset($_SESSION['rol'])) {
    header("Location: dashboard_" . htmlspecialchars($_SESSION['rol']) . ".php");
    exit();
}
$error = isset($_GET['error']) ? $_GET['error'] : 0;
?>

<?php if ($error == 1): ?>
    <div class="alert alert-danger">Usuario o clave incorrectos.</div>
<?php elseif ($error == 2): ?>
    <div class="alert alert-warning">No se encontró el código de tutor en sesión.</div>
<?php elseif ($error == 3): ?>
    <div class="alert alert-danger">Rol no reconocido.</div>
<?php endif; ?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Acceso - Escuela Sabatina</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../Public/css/style_login.css" />
</head>
<body>
    <div class="container login-container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
        <div class="login-box text-center p-4 shadow rounded" style="max-width: 400px; width: 100%; background: #fff;">
            
            <img src="https://cdn-icons-png.flaticon.com/512/3135/3135755.png" class="logo mb-3" alt="Logo Escuela" style="max-width: 80px;" />
            
            <h4 class="login-title mb-3">Portal Sabatino - Cursos</h4>

            <?php if ($error): ?>
                <div class="alert alert-danger" role="alert">
                    Usuario o clave incorrectos.
                </div>
            <?php endif; ?>

            <form action="../controllers/loginControlador.php" method="POST" class="text-start">
                <div class="mb-3">
                    <label for="usuario" class="form-label">Usuario</label>
                    <input
                        type="text"
                        name="usuario"
                        id="usuario"
                        class="form-control"
                        placeholder="Ingrese su usuario"
                        required
                        autocomplete="username"
                    />
                </div>
                <div class="mb-3">
                    <label for="clave" class="form-label">Clave</label>
                    <input
                        type="password"
                        name="clave"
                        id="clave"
                        class="form-control"
                        placeholder="Ingrese su contraseña"
                        required
                        autocomplete="current-password"
                    />
                </div>
                <div class="d-grid mt-3">
                    <button type="submit" class="btn btn-primary">Ingresar</button>
                </div>
            </form>

            <p class="mt-4 text-muted" style="font-size: 0.9rem;">
                Solo para Administrador y docentes registrados en el curso sabatino.
            </p>
        </div>
    </div>

    <!-- Bootstrap JS (opcional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
