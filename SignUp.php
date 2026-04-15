<?php
// Inicia la sesion por coherencia con el resto de pantallas del proyecto.
session_start();
// Carga la funcion de saneado de entradas.
require_once __DIR__ . "/database/securizar.php";
// Carga el conjunto de funciones de negocio y base de datos.
require_once __DIR__ . "/database/funcionesDB.php";

// Mensaje que se mostrara si algo falla durante el registro.
$msgRegistro = "";

// Procesa el alta de usuario cuando el formulario llega por POST.
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Limpia y recoge todos los campos del formulario.
    $nombre = securizar($_POST["nombre"] ?? "");
    $email = securizar($_POST["email"] ?? "");
    $pass = securizar($_POST["password"] ?? "");
    $rol = securizar($_POST["rol"] ?? "acompanante");

    // Valida que los campos obligatorios no esten vacios.
    if ($nombre === "" || $email === "" || $pass === "") {
        $msgRegistro = "Rellena todos los campos obligatorios.";
    } else {        
        // Intenta registrar al usuario con el rol elegido.
        $resultado = registrarUsuario($nombre, $email, $pass, ($rol === 'organizador') ? 'organizador' : 'acompanante');
        // Si el registro se completa, se redirige al login con mensaje de exito.
        if ($resultado['ok']) {
            header("Location: login.php?msg=success");
            exit();
        }
        // Si falla, se muestra el mensaje devuelto por la capa de negocio.
        $msgRegistro = $resultado['msg'];
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Metadatos y estilos de la pantalla de registro. -->
    <meta charset="UTF-8">
    <title>Gropper - Registro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="./views/estilo.css?v=20260407b">
</head>
<body>
    <!-- Menu comun superior. -->
    <?php include_once "./views/menu.php"; ?>
    <!-- Contenedor del formulario de alta. -->
    <div class="auth-container">
        <div class="auth-card text-center shadow">
            <i class="fas fa-plane-departure fa-2x text-primary mb-3"></i>
            <h2 class="fw-bold">Crea tu cuenta</h2>
            <p class="text-muted small mb-4">Empieza a organizar tus viajes</p>

            <!-- Muestra el error de registro si existe. -->
            <?php if ($msgRegistro !== ""): ?>
                <div class="alert alert-warning py-2 small"><?php echo $msgRegistro; ?></div>
            <?php endif; ?>

            <!-- Formulario principal de creacion de cuenta. -->
            <form action="SignUp.php" method="POST">
                <div class="text-start">
                    <label class="form-label small fw-bold">Nombre completo</label>
                    <input type="text" name="nombre" class="form-control" required>
                    <label class="form-label small fw-bold mt-2">Email</label>
                    <input type="email" name="email" class="form-control" required>
                    <label class="form-label small fw-bold mt-2">Contraseña</label>
                    <input type="password" name="password" class="form-control" required>
                    <label class="form-label d-block text-center mt-3 small fw-bold">Cual es tu rol?</label>
                    <div class="d-flex gap-2 mt-2 mb-4">
                        <input type="radio" class="btn-check" name="rol" id="org" value="organizador" checked>
                        <label class="btn btn-role" for="org">Organizador</label>
                        <input type="radio" class="btn-check" name="rol" id="aco" value="acompanante">
                        <label class="btn btn-role" for="aco">Acompanante</label>
                    </div>
                </div>
                <button type="submit" class="btn-auth shadow-sm">Registrarse</button>
            </form>
            <!-- Enlace de apoyo para usuarios que ya tienen cuenta. -->
            <p class="mt-4 small">Ya tienes cuenta? <a href="login.php" class="text-primary fw-bold text-decoration-none">Inicia sesion</a></p>
        </div>
    </div>
</body>
</html>
