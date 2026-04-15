<?php

// Añade un servicio a un viaje respetando presupuesto y evitando duplicados.
function anadirServicioAViaje($viajeId, $servicioId, $personas = 1): array
{
    // Carga el viaje al que se quiere añadir el servicio.
    $viaje = obtenerViaje($viajeId);
    if (!$viaje) {
        return ['ok' => false, 'msg' => 'Viaje no encontrado.'];
    }

    // Carga el servicio elegido dentro del catalogo sincronizado.
    $servicio = obtenerServicioPorId($servicioId);
    if (!$servicio) {
        return ['ok' => false, 'msg' => 'Servicio no encontrado.'];
    }

    // Asegura al menos una persona y calcula el precio total aplicado para el grupo.
    $personas = max(1, (int) $personas);
    $precioAplicado = round((float) $servicio['precio_total'] * $personas, 2);
    // Impide superar el presupuesto limite del viaje.
    if ((getTotalViaje($viajeId) + $precioAplicado) > (float) $viaje['presupuesto_limite']) {
        return ['ok' => false, 'msg' => 'Este servicio supera el presupuesto disponible.'];
    }

    // Evita reservar dos veces el mismo servicio en un mismo viaje.
    foreach (obtenerReservasViaje($viajeId) as $reserva) {
        if ((int) $reserva['servicio_id'] === (int) $servicioId) {
            return ['ok' => false, 'msg' => 'Ese servicio ya esta anadido al viaje.'];
        }
    }

    // Inserta la reserva en la base de datos.
    $con = conectar();
    $stmt = mysqli_prepare($con, 'INSERT INTO reserva (viaje_id, servicio_id, personas, precio_aplicado) VALUES (?, ?, ?, ?)');
    mysqli_stmt_bind_param($stmt, 'iiid', $viajeId, $servicioId, $personas, $precioAplicado);
    $ok = mysqli_stmt_execute($stmt);
    $error = mysqli_error($con);
    mysqli_close($con);

    // Devuelve el resultado de la operacion.
    return ['ok' => $ok, 'msg' => $ok ? 'Servicio anadido al viaje.' : ('No se pudo guardar el servicio. ' . $error)];
}

// Recupera todas las reservas de un viaje junto con el total pagado por cada una.
function obtenerReservasViaje($viajeId): array
{
    $con = conectar();
    $stmt = mysqli_prepare($con, '
        SELECT
            r.id,
            r.viaje_id,
            r.servicio_id,
            r.personas,
            r.precio_aplicado,
            COALESCE(SUM(p.cantidad), 0) AS total_pagado
        FROM reserva r
        LEFT JOIN pago p ON p.reserva_id = r.id
        WHERE r.viaje_id = ?
        GROUP BY r.id, r.viaje_id, r.servicio_id, r.personas, r.precio_aplicado
        ORDER BY r.id ASC
    ');
    mysqli_stmt_bind_param($stmt, 'i', $viajeId);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $reservasDb = $resultado ? fetch_all_assoc($resultado) : [];
    mysqli_close($con);

    // Mezcla cada fila de reserva con la informacion completa del servicio correspondiente.
    $reservas = [];
    foreach ($reservasDb as $fila) {
        $servicio = obtenerServicioPorId((int) $fila['servicio_id']);
        // Si el servicio ya no existe, se salta para no romper el listado.
        if (!$servicio) {
            continue;
        }

        $reservaId = (int) $fila['id'];
        $fila['precio_aplicado'] = (float) $fila['precio_aplicado'];
        $fila['total_pagado'] = (float) $fila['total_pagado'];
        $fila['usuario_ha_pagado'] = 0;
        // Fusiona datos de servicio y reserva en una unica estructura util para las vistas.
        $reservas[] = array_merge($servicio, $fila, ['id' => $reservaId, 'reserva_id' => $reservaId]);
    }

    return $reservas;
}

// Recupera las reservas de un viaje desde la perspectiva de un usuario concreto.
function obtenerReservasViajeParaUsuario(int $viajeId, int $usuarioId): array
{
    $con = conectar();
    $stmt = mysqli_prepare($con, '
        SELECT
            r.id,
            r.viaje_id,
            r.servicio_id,
            r.pagado_por_id,
            r.personas,
            r.precio_aplicado,
            COALESCE(SUM(p.cantidad), 0) AS total_pagado,
            MAX(CASE WHEN p.usuario_id = ? THEN 1 ELSE 0 END) AS usuario_ha_pagado
        FROM reserva r
        LEFT JOIN pago p ON p.reserva_id = r.id
        WHERE r.viaje_id = ?
        GROUP BY r.id, r.viaje_id, r.servicio_id, r.pagado_por_id, r.personas, r.precio_aplicado
        ORDER BY r.id ASC
    ');
    mysqli_stmt_bind_param($stmt, 'ii', $usuarioId, $viajeId);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $reservasDb = $resultado ? fetch_all_assoc($resultado) : [];
    mysqli_close($con);

    // Enriquecimiento identico al anterior, añadiendo ademas si el usuario ya pago o no.
    $reservas = [];
    foreach ($reservasDb as $fila) {
        $servicio = obtenerServicioPorId((int) $fila['servicio_id']);
        if (!$servicio) {
            continue;
        }

        $reservaId = (int) $fila['id'];
        $fila['precio_aplicado'] = (float) $fila['precio_aplicado'];
        $fila['total_pagado'] = (float) $fila['total_pagado'];
        $fila['usuario_ha_pagado'] = (int) ($fila['usuario_ha_pagado'] ?? 0);
        $reservas[] = array_merge($servicio, $fila, ['id' => $reservaId, 'reserva_id' => $reservaId]);
    }

    return $reservas;
}

// Permite al organizador borrar una reserva y sus pagos asociados.
function eliminarReservaOrganizador(int $reservaId, int $organizadorId): bool
{
    // Abre conexion y prepara una transaccion para garantizar coherencia.
    $conexion = conectar();
    mysqli_begin_transaction($conexion);

    try {
        // Borra los pagos asociados a la reserva validando primero la propiedad del viaje.
        $stmt = mysqli_prepare($conexion, '
            DELETE p
            FROM pago p
            JOIN reserva r ON r.id = p.reserva_id
            JOIN viaje v ON v.id = r.viaje_id
            WHERE r.id = ? AND v.organizador_id = ?
        ');
        mysqli_stmt_bind_param($stmt, 'ii', $reservaId, $organizadorId);
        if (!$stmt || !mysqli_stmt_execute($stmt)) {
            throw new RuntimeException('No se pudieron borrar los pagos.');
        }

        // Borra la propia reserva validando igualmente que pertenece al organizador.
        $stmt = mysqli_prepare($conexion, '
            DELETE r
            FROM reserva r
            JOIN viaje v ON v.id = r.viaje_id
            WHERE r.id = ? AND v.organizador_id = ?
        ');
        mysqli_stmt_bind_param($stmt, 'ii', $reservaId, $organizadorId);
        $ok = $stmt && mysqli_stmt_execute($stmt);
        $filas = $stmt ? mysqli_stmt_affected_rows($stmt) : 0;
        if (!$ok || $filas <= 0) {
            throw new RuntimeException('No se pudo borrar la reserva.');
        }

        // Confirma los cambios si todo fue bien.
        mysqli_commit($conexion);
        mysqli_close($conexion);
        return true;
    } catch (Throwable $e) {
        // Si algo falla, revierte la transaccion y devuelve false.
        mysqli_rollback($conexion);
        mysqli_close($conexion);
        return false;
    }
}

// Obtiene una reserva concreta por su id con informacion basica del servicio y pagos.
function obtenerReservaPorId(int $reservaId): ?array
{
    $con = conectar();
    $stmt = mysqli_prepare($con, '
        SELECT
            r.id,
            r.viaje_id,
            r.pagado_por_id,
            r.personas,
            COALESCE(r.precio_aplicado, s.precio_total) AS precio_aplicado,
            s.descripcion,
            s.precio_total,
            COALESCE((SELECT SUM(p.cantidad) FROM pago p WHERE p.reserva_id = r.id), 0) AS total_pagado
        FROM reserva r
        JOIN servicio s ON s.id = r.servicio_id
        WHERE r.id = ?
        LIMIT 1
    ');
    mysqli_stmt_bind_param($stmt, 'i', $reservaId);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $reserva = $resultado ? mysqli_fetch_assoc($resultado) : null;
    mysqli_close($con);
    return $reserva ?: null;
}

// Calcula la parte proporcional que corresponde pagar a cada participante.
function calcularParteReserva(float $precioTotal, int $personas): float
{
    return round($precioTotal / max(1, $personas), 2);
}

// Registra el pago de la parte proporcional de un usuario para una reserva.
function registrarPagoParteReserva(int $reservaId, int $usuarioId): bool
{
    // Carga precio total y numero de personas de la reserva.
    $con = conectar();
    $stmt = mysqli_prepare($con, '
        SELECT COALESCE(r.precio_aplicado, s.precio_total) AS precio_total, r.personas
        FROM reserva r
        JOIN servicio s ON s.id = r.servicio_id
        WHERE r.id = ?
        LIMIT 1
    ');
    mysqli_stmt_bind_param($stmt, 'i', $reservaId);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $reserva = $resultado ? mysqli_fetch_assoc($resultado) : null;
    // Si la reserva no existe, no puede registrarse el pago.
    if (!$reserva) {
        mysqli_close($con);
        return false;
    }

    // Calcula la parte teorica que corresponde a cada integrante.
    $precioTotal = (float) ($reserva['precio_total'] ?? 0);
    $personas = (int) ($reserva['personas'] ?? 1);
    $parte = calcularParteReserva($precioTotal, $personas);

    // Evita registrar dos pagos del mismo usuario para la misma reserva.
    $stmt = mysqli_prepare($con, 'SELECT 1 FROM pago WHERE reserva_id = ? AND usuario_id = ? LIMIT 1');
    mysqli_stmt_bind_param($stmt, 'ii', $reservaId, $usuarioId);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    if ($resultado && mysqli_num_rows($resultado) > 0) {
        mysqli_close($con);
        return false;
    }

    // Calcula cuanto queda pendiente de pago en total.
    $stmt = mysqli_prepare($con, 'SELECT COALESCE(SUM(cantidad), 0) AS pagado FROM pago WHERE reserva_id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $reservaId);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $pagado = $resultado ? (float) (mysqli_fetch_assoc($resultado)['pagado'] ?? 0) : 0.0;
    $pendiente = max(0, round($precioTotal - $pagado, 2));
    if ($pendiente <= 0) {
        mysqli_close($con);
        return false;
    }

    // Inserta el pago real con la cantidad final aplicable.
    $cantidad = min($parte, $pendiente);
    $stmt = mysqli_prepare($con, 'INSERT INTO pago (reserva_id, usuario_id, cantidad) VALUES (?, ?, ?)');
    mysqli_stmt_bind_param($stmt, 'iid', $reservaId, $usuarioId, $cantidad);
    $ok = mysqli_stmt_execute($stmt);
    if (!$ok) {
        mysqli_close($con);
        return false;
    }

    // Recalcula el total pagado para ver si la reserva ya quedo completamente abonada.
    $stmt = mysqli_prepare($con, 'SELECT COALESCE(SUM(cantidad), 0) AS pagado FROM pago WHERE reserva_id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $reservaId);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $pagadoFinal = $resultado ? (float) (mysqli_fetch_assoc($resultado)['pagado'] ?? 0) : 0.0;
    $pagadoPor = $pagadoFinal >= round($precioTotal, 2) ? $usuarioId : null;

    // Si el servicio ya esta cubierto por completo, marca quien figura como pagador final.
    if ($pagadoPor !== null) {
        $stmt = mysqli_prepare($con, 'UPDATE reserva SET pagado_por_id = ? WHERE id = ?');
        mysqli_stmt_bind_param($stmt, 'ii', $pagadoPor, $reservaId);
        mysqli_stmt_execute($stmt);
    }

    // Devuelve true al completar el registro.
    mysqli_close($con);
    return true;
}

// Suma el importe aplicado de todas las reservas de un viaje.
function getTotalViaje($viajeId): float
{
    $total = 0.0;
    foreach (obtenerReservasViaje($viajeId) as $reserva) {
        $total += (float) $reserva['precio_aplicado'];
    }
    return round($total, 2);
}

// Devuelve la lista de pagos realizados sobre una reserva.
function obtenerPagosPorReserva($reservaId): array
{
    $con = conectar();
    $stmt = mysqli_prepare($con, '
        SELECT p.usuario_id, p.cantidad, p.fecha_pago, u.nombre
        FROM pago p
        JOIN usuario u ON u.id = p.usuario_id
        WHERE p.reserva_id = ?
        ORDER BY p.fecha_pago ASC
    ');
    mysqli_stmt_bind_param($stmt, 'i', $reservaId);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $pagos = $resultado ? fetch_all_assoc($resultado) : [];
    mysqli_close($con);
    return $pagos;
}
