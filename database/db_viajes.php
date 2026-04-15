<?php

// Crea un viaje nuevo comprobando primero destino, presupuesto y fechas.
function crearViaje($organizadorId, $destino, $presupuesto, $fechaInicio, $fechaFin): array
{
    // Recupera la lista de destinos soportados por el catalogo.
    $destinos = getDestinos();
    // Impide crear viajes hacia destinos fuera del catalogo.
    if (!in_array($destino, $destinos, true)) {
        return ['ok' => false, 'msg' => 'Destino no valido.'];
    }

    // Valida que el presupuesto tenga un valor util.
    if ($presupuesto <= 0) {
        return ['ok' => false, 'msg' => 'El presupuesto debe ser mayor que 0.'];
    }

    // Valida que la fecha de fin no sea anterior a la fecha de inicio.
    if ($fechaInicio !== '' && $fechaFin !== '' && strtotime($fechaFin) < strtotime($fechaInicio)) {
        return ['ok' => false, 'msg' => 'La fecha de fin no puede ser anterior a la de inicio.'];
    }

    // Inserta el viaje en la base de datos.
    $con = conectar();
    $fechaInicioSql = ($fechaInicio !== '') ? $fechaInicio : null;
    $fechaFinSql = ($fechaFin !== '') ? $fechaFin : null;
    $stmt = mysqli_prepare($con, 'INSERT INTO viaje (organizador_id, destino, presupuesto_limite, fecha_inicio, fecha_fin) VALUES (?, ?, ?, ?, ?)');
    mysqli_stmt_bind_param($stmt, 'isdss', $organizadorId, $destino, $presupuesto, $fechaInicioSql, $fechaFinSql);
    $ok = mysqli_stmt_execute($stmt);
    $viajeId = $ok ? (int) mysqli_insert_id($con) : 0;
    $error = mysqli_error($con);
    mysqli_close($con);

    // Devuelve el resultado de la creacion junto con el id generado si existe.
    return ['ok' => $ok, 'msg' => $ok ? 'Viaje creado correctamente.' : ('Error al crear el viaje. ' . $error), 'viaje_id' => $viajeId];
}

// Obtiene todos los viajes creados por un organizador, ordenados del mas reciente al mas antiguo.
function obtenerViajesOrganizador($organizadorId): array
{
    $con = conectar();
    $stmt = mysqli_prepare($con, 'SELECT * FROM viaje WHERE organizador_id = ? ORDER BY id DESC');
    mysqli_stmt_bind_param($stmt, 'i', $organizadorId);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $viajes = $resultado ? fetch_all_assoc($resultado) : [];
    mysqli_close($con);
    return $viajes;
}

// Recupera un viaje individual por su id.
function obtenerViaje($viajeId): ?array
{
    $con = conectar();
    $stmt = mysqli_prepare($con, 'SELECT * FROM viaje WHERE id = ? LIMIT 1');
    mysqli_stmt_bind_param($stmt, 'i', $viajeId);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $viaje = $resultado ? mysqli_fetch_assoc($resultado) : null;
    mysqli_close($con);
    return $viaje ?: null;
}

// Obtiene las invitaciones pendientes de respuesta para un acompanante.
function obtenerInvitacionesPendientes($usuarioId): array
{
    $con = conectar();
    $stmt = mysqli_prepare($con, "
        SELECT
            v.*,
            u.nombre AS organizador_nombre,
            1 + COALESCE(SUM(CASE WHEN pv2.estado_invitacion = 'aceptada' THEN 1 ELSE 0 END), 0) AS personas_confirmadas
        FROM participante_viaje pv
        JOIN viaje v ON v.id = pv.viaje_id
        JOIN usuario u ON u.id = v.organizador_id
        LEFT JOIN participante_viaje pv2 ON pv2.viaje_id = v.id
        WHERE pv.usuario_id = ? AND pv.estado_invitacion = 'pendiente'
        GROUP BY v.id, u.nombre
        ORDER BY v.id DESC
    ");
    mysqli_stmt_bind_param($stmt, 'i', $usuarioId);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $invitaciones = $resultado ? fetch_all_assoc($resultado) : [];
    mysqli_close($con);
    return $invitaciones;
}

// Actualiza el estado de una invitacion como aceptada o rechazada.
function responderInvitacion($usuarioId, $viajeId, $estado): bool
{
    // Fuerza el estado a uno de los dos valores permitidos en esta accion.
    $estado = ($estado === 'aceptada') ? 'aceptada' : 'rechazada';
    $con = conectar();
    $stmt = mysqli_prepare($con, 'UPDATE participante_viaje SET estado_invitacion = ? WHERE viaje_id = ? AND usuario_id = ?');
    mysqli_stmt_bind_param($stmt, 'sii', $estado, $viajeId, $usuarioId);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $ok;
}

// Devuelve los viajes donde el usuario ya ha aceptado participar.
function obtenerViajesAceptados($usuarioId): array
{
    $con = conectar();
    $stmt = mysqli_prepare($con, "
        SELECT
            v.*,
            u.nombre AS organizador_nombre,
            1 + COALESCE(SUM(CASE WHEN pv2.estado_invitacion = 'aceptada' THEN 1 ELSE 0 END), 0) AS personas_confirmadas
        FROM participante_viaje pv
        JOIN viaje v ON v.id = pv.viaje_id
        JOIN usuario u ON u.id = v.organizador_id
        LEFT JOIN participante_viaje pv2 ON pv2.viaje_id = v.id
        WHERE pv.usuario_id = ? AND pv.estado_invitacion = 'aceptada'
        GROUP BY v.id, u.nombre
        ORDER BY v.id DESC
    ");
    mysqli_stmt_bind_param($stmt, 'i', $usuarioId);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $viajes = $resultado ? fetch_all_assoc($resultado) : [];
    mysqli_close($con);
    return $viajes;
}

// Devuelve la lista de participantes de un viaje junto con su estado de invitacion.
function obtenerParticipantesViaje($viajeId): array
{
    $con = conectar();
    $stmt = mysqli_prepare($con, '
        SELECT u.id, u.nombre, u.email, pv.estado_invitacion
        FROM participante_viaje pv
        JOIN usuario u ON u.id = pv.usuario_id
        WHERE pv.viaje_id = ?
        ORDER BY u.nombre ASC
    ');
    mysqli_stmt_bind_param($stmt, 'i', $viajeId);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $participantes = $resultado ? fetch_all_assoc($resultado) : [];
    mysqli_close($con);
    return $participantes;
}

// Recupera los datos basicos del organizador de un viaje.
function obtenerOrganizadorViaje($viajeId): ?array
{
    $con = conectar();
    $stmt = mysqli_prepare($con, '
        SELECT u.id, u.nombre
        FROM viaje v
        JOIN usuario u ON u.id = v.organizador_id
        WHERE v.id = ?
        LIMIT 1
    ');
    mysqli_stmt_bind_param($stmt, 'i', $viajeId);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $organizador = $resultado ? mysqli_fetch_assoc($resultado) : null;
    mysqli_close($con);
    return $organizador ?: null;
}

// Invita a un usuario al viaje localizandolo por su email.
function invitarParticipante($viajeId, $organizadorId, $email): array
{
    // Abre la conexion con la base de datos.
    $con = conectar();

    // Verifica que el viaje exista y pertenezca realmente al organizador actual.
    $stmt = mysqli_prepare($con, 'SELECT id FROM viaje WHERE id = ? AND organizador_id = ?');
    mysqli_stmt_bind_param($stmt, 'ii', $viajeId, $organizadorId);
    mysqli_stmt_execute($stmt);
    $resultadoViaje = mysqli_stmt_get_result($stmt);
    if (!$resultadoViaje || !mysqli_fetch_assoc($resultadoViaje)) {
        mysqli_close($con);
        return ['ok' => false, 'msg' => 'Viaje no valido.'];
    }

    // Busca el usuario destinatario de la invitacion por email.
    $stmt = mysqli_prepare($con, 'SELECT id FROM usuario WHERE email = ? LIMIT 1');
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $resultadoUsuario = mysqli_stmt_get_result($stmt);
    $usuario = $resultadoUsuario ? mysqli_fetch_assoc($resultadoUsuario) : null;
    if (!$usuario) {
        mysqli_close($con);
        return ['ok' => false, 'msg' => 'No existe un usuario con ese email.'];
    }

    $usuarioId = (int) $usuario['id'];
    // Evita que el organizador se invite a si mismo.
    if ($usuarioId === (int) $organizadorId) {
        mysqli_close($con);
        return ['ok' => false, 'msg' => 'No puedes invitarte a ti mismo.'];
    }

    // Comprueba si ya existia una invitacion previa para ese usuario y viaje.
    $stmt = mysqli_prepare($con, 'SELECT estado_invitacion FROM participante_viaje WHERE viaje_id = ? AND usuario_id = ? LIMIT 1');
    mysqli_stmt_bind_param($stmt, 'ii', $viajeId, $usuarioId);
    mysqli_stmt_execute($stmt);
    $resultadoInv = mysqli_stmt_get_result($stmt);
    $existente = $resultadoInv ? mysqli_fetch_assoc($resultadoInv) : null;

    if ($existente) {
        mysqli_close($con);
        return ['ok' => false, 'msg' => 'Ese usuario ya fue invitado a este viaje.'];
    }

    // Inserta la invitacion con estado inicial pendiente.
    $estado = 'pendiente';
    $stmt = mysqli_prepare($con, 'INSERT INTO participante_viaje (viaje_id, usuario_id, estado_invitacion) VALUES (?, ?, ?)');
    mysqli_stmt_bind_param($stmt, 'iis', $viajeId, $usuarioId, $estado);
    $ok = mysqli_stmt_execute($stmt);
    $error = mysqli_error($con);
    mysqli_close($con);

    // Devuelve el resultado final del envio de la invitacion.
    return ['ok' => $ok, 'msg' => $ok ? 'Invitacion enviada.' : ('No se pudo enviar la invitacion. ' . $error)];
}

// Borra un viaje completo junto con sus pagos, reservas e invitaciones asociadas.
function borrarViaje($viajeId, $organizadorId): array
{
    // Verifica primero que el viaje pertenece al organizador que solicita el borrado.
    $con = conectar();
    $stmt = mysqli_prepare($con, 'SELECT id FROM viaje WHERE id = ? AND organizador_id = ? LIMIT 1');
    mysqli_stmt_bind_param($stmt, 'ii', $viajeId, $organizadorId);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    if (!$resultado || !mysqli_fetch_assoc($resultado)) {
        mysqli_close($con);
        return ['ok' => false, 'msg' => 'No tienes permiso para borrar este viaje.'];
    }

    // Inicia una transaccion para borrar todo de manera atomica.
    mysqli_begin_transaction($con);
    try {
        // Borra primero los pagos relacionados con las reservas del viaje.
        $stmt = mysqli_prepare($con, 'DELETE p FROM pago p JOIN reserva r ON p.reserva_id = r.id WHERE r.viaje_id = ?');
        mysqli_stmt_bind_param($stmt, 'i', $viajeId);
        mysqli_stmt_execute($stmt);

        // Borra las reservas del viaje.
        $stmt = mysqli_prepare($con, 'DELETE FROM reserva WHERE viaje_id = ?');
        mysqli_stmt_bind_param($stmt, 'i', $viajeId);
        mysqli_stmt_execute($stmt);

        // Borra las invitaciones o participaciones registradas.
        $stmt = mysqli_prepare($con, 'DELETE FROM participante_viaje WHERE viaje_id = ?');
        mysqli_stmt_bind_param($stmt, 'i', $viajeId);
        mysqli_stmt_execute($stmt);

        // Finalmente borra el propio viaje.
        $stmt = mysqli_prepare($con, 'DELETE FROM viaje WHERE id = ?');
        mysqli_stmt_bind_param($stmt, 'i', $viajeId);
        mysqli_stmt_execute($stmt);

        // Si todo ha ido bien, confirma la transaccion.
        mysqli_commit($con);
        mysqli_close($con);
        return ['ok' => true, 'msg' => 'Viaje borrado correctamente.'];
    } catch (Throwable $e) {
        // Si algo falla, se revierte todo para no dejar datos huérfanos.
        mysqli_rollback($con);
        mysqli_close($con);
        return ['ok' => false, 'msg' => 'No se pudo borrar el viaje.'];
    }
}
