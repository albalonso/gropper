<?php

// Registra un nuevo usuario comprobando antes si el email ya existe.
function registrarUsuario($nombre, $email, $pass, $tipo): array
{
    // Abre la conexion con la base de datos.
    $con = conectar();
    // Normaliza los datos basicos recibidos del formulario.
    $nombre = trim((string) $nombre);
    $email = trim((string) $email);
    // Limita el rol a los dos roles soportados por la aplicacion.
    $tipo = ($tipo === 'organizador') ? 'organizador' : 'acompanante';

    // Comprueba si ya existe un usuario con el mismo email.
    $stmt = mysqli_prepare($con, 'SELECT id FROM usuario WHERE email = ?');
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $existe = mysqli_stmt_get_result($stmt);
    // Si existe, se cierra la conexion y se devuelve el error de negocio.
    if ($existe && mysqli_fetch_assoc($existe)) {
        mysqli_close($con);
        return ['ok' => false, 'msg' => 'El email ya existe.'];
    }

    // Genera un hash seguro de la contraseña en lugar de guardarla en texto plano.
    $hash = password_hash($pass, PASSWORD_DEFAULT);
    // Inserta el nuevo usuario en la tabla.
    $stmt = mysqli_prepare($con, 'INSERT INTO usuario (nombre, email, password_hash, tipo) VALUES (?, ?, ?, ?)');
    mysqli_stmt_bind_param($stmt, 'ssss', $nombre, $email, $hash, $tipo);
    $ok = mysqli_stmt_execute($stmt);
    $error = mysqli_error($con);
    mysqli_close($con);

    // Devuelve si la operacion se ha completado correctamente junto con un mensaje util.
    return ['ok' => $ok, 'msg' => $ok ? 'Cuenta creada con exito.' : ('No se pudo crear la cuenta. ' . $error)];
}

// Valida un inicio de sesion consultando el usuario por email y verificando la contraseña.
function login($email, $pass): ?array
{
    // Abre la conexion con la base de datos.
    $con = conectar();
    // Normaliza el email recibido.
    $email = trim((string) $email);

    $stmt = mysqli_prepare($con, 'SELECT * FROM usuario WHERE email = ? LIMIT 1');
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $usuario = $resultado ? mysqli_fetch_assoc($resultado) : null;
    mysqli_close($con);

    // Si el usuario existe y la contraseña coincide con el hash guardado, devuelve sus datos.
    if ($usuario && password_verify($pass, $usuario['password_hash'])) {
        return $usuario;
    }

    // Si algo no cuadra, devuelve null para indicar login fallido.
    return null;
}

// Recupera un usuario concreto a partir de su identificador.
function obtenerUsuarioPorId(int $usuarioId): ?array
{
    $con = conectar();
    $stmt = mysqli_prepare($con, 'SELECT * FROM usuario WHERE id = ? LIMIT 1');
    mysqli_stmt_bind_param($stmt, 'i', $usuarioId);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $usuario = $resultado ? mysqli_fetch_assoc($resultado) : null;
    mysqli_close($con);
    // Devuelve el usuario si existe; si no, devuelve null.
    return $usuario ?: null;
}

// Comprueba que la sesion tenga un usuario valido y refresca sus datos en memoria.
function asegurarUsuarioSesion(): ?array
{
    // Si la sesion ni siquiera esta activa, no se puede validar nada.
    if (session_status() !== PHP_SESSION_ACTIVE) {
        return null;
    }

    // Si no hay id de usuario en sesion, no existe autenticacion valida.
    if (!isset($_SESSION['usuario_id'])) {
        return null;
    }

    // Carga el usuario real desde base de datos para evitar sesiones obsoletas.
    $usuario = obtenerUsuarioPorId((int) $_SESSION['usuario_id']);
    // Si el usuario ya no existe, limpia la sesion completa.
    if (!$usuario) {
        unset($_SESSION['usuario_id'], $_SESSION['usuario_nombre'], $_SESSION['usuario_email'], $_SESSION['usuario_rol'], $_SESSION['viaje_activo_id']);
        return null;
    }

    // Refresca los datos de sesion con la informacion vigente del usuario.
    $_SESSION['usuario_id'] = (int) $usuario['id'];
    $_SESSION['usuario_nombre'] = $usuario['nombre'];
    $_SESSION['usuario_email'] = $usuario['email'];
    $_SESSION['usuario_rol'] = $usuario['tipo'];

    // Devuelve el usuario validado para que pueda usarse inmediatamente.
    return $usuario;
}
