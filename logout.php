<?php
// Recupera la sesion actual para poder cerrarla correctamente.
session_start();

// 1. Limpiamos todas las variables de sesión
session_unset();

// 2. Destruimos la sesión en el servidor
session_destroy();

// 3. Si usaste cookies para el "recuérdame" (como en sesiones.php), la eliminamos
if (isset($_COOKIE['usuario_token'])) {
    setcookie("usuario_token", "", time() - 3600, "/");
}

// 4. Redirigimos al login con un mensaje de confirmación
header("Location: login.php?mensaje=Has cerrado sesión correctamente. ¡Vuelve pronto!");
exit();
?>
