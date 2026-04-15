<?php
// Inicia o recupera la sesion activa del usuario.
session_start();
// Carga las funciones comunes de acceso a datos y autenticacion.
require_once __DIR__ . "/database/funcionesDB.php";
// Comprueba que el usuario siga autenticado; si no, lo devuelve al login.
if (!asegurarUsuarioSesion()) {
    header("Location: login.php");
    exit();
}

// Si el rol almacenado en sesion es organizador, se envia a su panel propio.
if (($_SESSION['usuario_rol'] ?? '') === 'organizador') {
    header("Location: gestionar_viajes.php");
    exit();
}

// Si no es organizador, se dirige al dashboard de acompanante.
header("Location: acompanante_dashboard.php");
exit();
