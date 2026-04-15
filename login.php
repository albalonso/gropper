<?php
// Inicia la sesion para poder guardar los datos del usuario al autenticarlo.
session_start();
// Carga la limpieza de entradas.
require_once __DIR__ . "/database/securizar.php";
// Carga las funciones de usuarios y demas modulos del proyecto.
require_once __DIR__ . "/database/funcionesDB.php";

// Variable para mostrar errores de autenticacion.
$emailErr = "";
// Mensaje opcional recibido tras un registro correcto.
$registroOk = (isset($_GET['msg']) && $_GET['msg'] === 'success') ? "Cuenta creada con exito. Ya puedes iniciar sesion." : "";

// Procesa el formulario de login cuando llega por POST.
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Limpia los campos del formulario.
    $email = securizar($_POST["email"] ?? "");
    $pass = securizar($_POST["password"] ?? "");

    // Intenta autenticar al usuario contra la base de datos.
    $usuarioLogin = login($email, $pass);

    // Si el login es correcto, guarda los datos basicos del usuario en sesion.
    if ($usuarioLogin !== null) {
        $_SESSION['usuario_id'] = $usuarioLogin['id'];
        $_SESSION['usuario_nombre'] = $usuarioLogin['nombre'];
        $_SESSION['usuario_email'] = $usuarioLogin['email'];
        $_SESSION['usuario_rol'] = $usuarioLogin['tipo'];
        // Redirige al dashboard comun para que este decida la pantalla final segun el rol.
        header("Location: dashboard.php");
        exit();
    }

    // Si falla la autenticacion, prepara el mensaje de error.
    $emailErr = "Credenciales incorrectas.";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Metadatos y estilos de la pantalla de login. -->
    <meta charset="UTF-8">
    <title>Gropper - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="./views/estilo.css?v=20260407b">
</head>
<body>
    <!-- Menu superior comun. -->
    <?php include_once "./views/menu.php"; ?>
    <!-- Tarjeta central con el formulario de acceso. -->
    <div class="auth-container">
        <div class="auth-card text-center shadow">
            <i class="fas fa-plane-departure fa-2x text-primary mb-3"></i>
            <h2 class="fw-bold">Bienvenido</h2>
            <p class="text-muted small mb-3">Inicia sesion en tu cuenta</p>

            <!-- Mensajes informativos de registro correcto o error de acceso. -->
            <?php if ($registroOk !== ""): ?><div class="alert alert-success py-2 small"><?php echo $registroOk; ?></div><?php endif; ?>
            <?php if ($emailErr !== ""): ?><div class="alert alert-danger py-2 small"><?php echo $emailErr; ?></div><?php endif; ?>

            <!-- Formulario de acceso del usuario. -->
            <form action="login.php" method="POST">
                <div class="text-start">
                    <label class="form-label small fw-bold">Email</label>
                    <input type="email" name="email" class="form-control" required>
                    <label class="form-label small fw-bold mt-2">Contraseña</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn-auth shadow-sm">Entrar</button>
            </form>
            <!-- Enlace de apoyo para llevar al registro si todavia no hay cuenta. -->
            <p class="mt-4 small">No tienes cuenta? <a href="SignUp.php" class="text-primary fw-bold text-decoration-none">Registrate</a></p>
        </div>
    </div>
</body>
</html>
