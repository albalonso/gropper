<?php

// Devuelve la lista de destinos disponibles a partir del catalogo estatico.
function getDestinos(): array
{
    return array_keys(catalogo_destinos());
}

// Devuelve la imagen asociada a un destino, usando Tokio como respaldo si no existe.
function getImagenDestino($destino): string
{
    $destinos = catalogo_destinos();
    return $destinos[$destino]['imagen'] ?? 'Tokio.jpg';
}

// Devuelve el aeropuerto principal de un destino.
function getAeropuertoDestino($destino): string
{
    $destinos = catalogo_destinos();
    return $destinos[$destino]['aeropuerto'] ?? ($destino . ' (INT)');
}

// Sincroniza la tabla servicio de la base de datos con el catalogo definido en PHP.
function sincronizarCatalogoServicios(): void
{
    // Carga el catalogo completo y prepara la conexion.
    $catalogo = catalogo_servicios();
    $con = conectar();
    // Aqui se guardan las claves que si deben existir tras la sincronizacion.
    $clavesValidas = [];

    // Recorre destino por destino y tipo por tipo.
    foreach ($catalogo as $destino => $bloques) {
        foreach ($bloques as $tipo => $items) {
            foreach ($items as $item) {
                // Construye una clave unica basada en tipo, destino y descripcion.
                $descripcion = (string) $item['descripcion'];
                $clavesValidas[] = $tipo . '|' . $destino . '|' . $descripcion;
                $precio = (float) $item['precio_total'];
                $imagen = $item['imagen'] ?? null;
                // Campo dejado preparado por si algun tipo necesitara otro tratamiento futuro.
                $precioPorPersona = ($tipo === 'alojamiento') ? 1 : 1;

                // Busca si el servicio ya existe en la base de datos.
                $stmt = mysqli_prepare($con, 'SELECT id FROM servicio WHERE tipo = ? AND descripcion = ? AND destino = ? LIMIT 1');
                mysqli_stmt_bind_param($stmt, 'sss', $tipo, $descripcion, $destino);
                mysqli_stmt_execute($stmt);
                $resultado = mysqli_stmt_get_result($stmt);
                $existente = $resultado ? mysqli_fetch_assoc($resultado) : null;

                // Si existe, actualiza sus datos principales.
                if ($existente) {
                    $servicioId = (int) $existente['id'];
                    $stmt = mysqli_prepare($con, 'UPDATE servicio SET precio_total = ?, imagen = ?, precio_por_persona = ? WHERE id = ?');
                    mysqli_stmt_bind_param($stmt, 'dsii', $precio, $imagen, $precioPorPersona, $servicioId);
                    mysqli_stmt_execute($stmt);
                    continue;
                }

                // Si no existe, lo inserta como nuevo servicio.
                $stmt = mysqli_prepare($con, 'INSERT INTO servicio (tipo, descripcion, precio_total, imagen, destino, precio_por_persona) VALUES (?, ?, ?, ?, ?, ?)');
                mysqli_stmt_bind_param($stmt, 'ssdssi', $tipo, $descripcion, $precio, $imagen, $destino, $precioPorPersona);
                mysqli_stmt_execute($stmt);
            }
        }
    }

    // Recupera todos los servicios existentes para detectar obsoletos.
    $resultado = mysqli_query($con, 'SELECT id, tipo, destino, descripcion FROM servicio');
    $serviciosDb = $resultado ? fetch_all_assoc($resultado) : [];
    // Elimina servicios que ya no esten en el catalogo siempre que no tengan reservas.
    foreach ($serviciosDb as $servicioDb) {
        $clave = $servicioDb['tipo'] . '|' . $servicioDb['destino'] . '|' . $servicioDb['descripcion'];
        if (in_array($clave, $clavesValidas, true)) {
            continue;
        }

        // Comprueba si el servicio esta siendo usado en alguna reserva.
        $servicioId = (int) $servicioDb['id'];
        $stmt = mysqli_prepare($con, 'SELECT COUNT(*) AS total FROM reserva WHERE servicio_id = ?');
        mysqli_stmt_bind_param($stmt, 'i', $servicioId);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $totalReservas = $res ? (int) (mysqli_fetch_assoc($res)['total'] ?? 0) : 0;

        // Solo borra servicios no usados para no romper reservas antiguas.
        if ($totalReservas === 0) {
            $stmt = mysqli_prepare($con, 'DELETE FROM servicio WHERE id = ?');
            mysqli_stmt_bind_param($stmt, 'i', $servicioId);
            mysqli_stmt_execute($stmt);
        }
    }

    // Cierra la conexion al terminar la sincronizacion.
    mysqli_close($con);
}

// Obtiene servicios desde la base de datos filtrando por tipo y/o destino.
function obtenerServicios(?string $tipo = null, ?string $destino = null): array
{
    // Antes de leer, se asegura de que la tabla servicio esta sincronizada con el catalogo.
    sincronizarCatalogoServicios();
    $catalogo = catalogo_servicios();
    $con = conectar();

    // Construye la consulta base y acumula filtros dinamicos.
    $sql = 'SELECT id, tipo, descripcion, precio_total, imagen, destino, precio_por_persona FROM servicio WHERE 1=1';
    $params = [];
    $types = '';

    // Si se pide un tipo concreto, añade ese filtro.
    if ($tipo !== null && $tipo !== '' && $tipo !== 'todos') {
        $sql .= ' AND tipo = ?';
        $params[] = $tipo;
        $types .= 's';
    }

    // Si se pide un destino concreto, añade ese filtro.
    if ($destino !== null && $destino !== '') {
        $sql .= ' AND destino = ?';
        $params[] = $destino;
        $types .= 's';
    }

    // Ordena por id para tener resultados estables.
    $sql .= ' ORDER BY id ASC';
    $stmt = mysqli_prepare($con, $sql);
    if ($types !== '') {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $filas = $resultado ? fetch_all_assoc($resultado) : [];
    mysqli_close($con);

    // Convierte las filas de base de datos en servicios completos enriquecidos con metadata del catalogo.
    $servicios = [];
    foreach ($filas as $fila) {
        $destinoActual = (string) ($fila['destino'] ?? '');
        $tipoActual = (string) ($fila['tipo'] ?? '');
        $meta = [];
        $enCatalogo = false;
        // Busca la metadata complementaria del servicio dentro del catalogo estatico.
        foreach (($catalogo[$destinoActual][$tipoActual] ?? []) as $item) {
            if (($item['descripcion'] ?? '') === ($fila['descripcion'] ?? '')) {
                $meta = $item;
                $enCatalogo = true;
                break;
            }
        }

        // Si por cualquier motivo ya no esta en el catalogo, se omite.
        if (!$enCatalogo) {
            continue;
        }

        // Construye la base comun del servicio con tipos y valores normalizados.
        $base = [
            'id' => (int) $fila['id'],
            'descripcion' => (string) $fila['descripcion'],
            'tipo' => $tipoActual,
            'destino' => $destinoActual,
            'precio_total' => (float) $fila['precio_total'],
            'imagen' => $fila['imagen'] ?: getImagenDestino($destinoActual),
            'precio_por_persona' => (int) ($fila['precio_por_persona'] ?? 1),
        ];
        // Combina datos de base de datos con los extras del catalogo.
        $servicios[] = array_merge($base, $meta);
    }

    // Devuelve la lista final de servicios lista para pintar en pantalla.
    return $servicios;
}

// Busca un servicio concreto recorriendo el listado completo ya enriquecido.
function obtenerServicioPorId($servicioId): ?array
{
    foreach (obtenerServicios() as $servicio) {
        if ((int) $servicio['id'] === (int) $servicioId) {
            return $servicio;
        }
    }
    return null;
}
