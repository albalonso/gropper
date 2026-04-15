<?php
// Inicia la sesion para saber que usuario esta consultando el itinerario.
session_start();
// Carga la utilidad de saneado de cadenas.
require_once __DIR__ . '/database/securizar.php';
// Carga toda la logica de viajes, reservas, pagos e invitaciones.
require_once __DIR__ . '/database/funcionesDB.php';

// Solo usuarios autenticados pueden ver el itinerario.
if (!asegurarUsuarioSesion()) {
    header('Location: login.php');
    exit();
}

// Datos basicos del usuario y del contexto del viaje.
$usuarioId = (int) $_SESSION['usuario_id'];
$esOrganizador = (($_SESSION['usuario_rol'] ?? '') === 'organizador');
$misViajes = $esOrganizador ? obtenerViajesOrganizador($usuarioId) : obtenerViajesAceptados($usuarioId);
$viajeId = (int) ($_GET['viaje_id'] ?? $_POST['viaje_id'] ?? $_SESSION['viaje_activo_id'] ?? 0);
$mensaje = '';

// Si no hay viaje elegido pero el usuario si tiene viajes, usa el primero.
if ($viajeId <= 0 && !empty($misViajes)) {
    $viajeId = (int) $misViajes[0]['id'];
}
if ($viajeId <= 0) {
    header('Location: dashboard.php');
    exit();
}

$viaje = obtenerViaje($viajeId);
if (!$viaje) {
    header('Location: dashboard.php');
    exit();
}

// Guarda el viaje activo en sesion para reutilizarlo en otras pantallas.
$_SESSION['viaje_activo_id'] = $viajeId;

// Permite al organizador eliminar reservas desde el itinerario.
if ($esOrganizador && isset($_GET['eliminar_reserva'])) {
    $rid = (int) $_GET['eliminar_reserva'];
    $ok = ($rid > 0) ? eliminarReservaOrganizador($rid, $usuarioId) : false;
    $estado = $ok ? 'ok' : 'error';
    header('Location: carrito.php?viaje_id=' . $viajeId . '&delete=' . $estado);
    exit();
}

// Permite al organizador invitar participantes por email.
if ($esOrganizador && $_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'invitar') {
    $resultado = invitarParticipante($viajeId, $usuarioId, securizar($_POST['invitar_email']));
    $mensaje = $resultado['msg'];
}

if (isset($_GET['delete'])) {
    $mensaje = $_GET['delete'] === 'ok' ? 'Servicio eliminado.' : 'No se pudo eliminar el servicio.';
}

// Carga todas las reservas y calcula las metricas economicas del viaje.
$reservas = obtenerReservasViajeParaUsuario($viajeId, $usuarioId);
$presupuesto = (float) ($viaje['presupuesto_limite'] ?? 0);
$total = getTotalViaje($viajeId);
$disponible = round($presupuesto - $total, 2);
$participantes = obtenerParticipantesViaje($viajeId);
$organizador = obtenerOrganizadorViaje($viajeId);
$confirmados = 1;
foreach ($participantes as $participante) {
    if (($participante['estado_invitacion'] ?? '') === 'aceptada') {
        $confirmados++;
    }
}
$gastoPersona = round($total / max(1, $confirmados), 2);
$porcentaje = $presupuesto > 0 ? min(100, round(($total / $presupuesto) * 100)) : 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Metadatos y hojas de estilo del itinerario. -->
    <meta charset="UTF-8">
    <title>Itinerario - Gropper</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="./views/estilo.css?v=20260408a">
</head>
<body>
<?php /* Menu comun superior. */ ?>
<?php include_once './views/menu.php'; ?>
<div class="container mt-4 mb-5">
    <!-- Cabecera del itinerario con destino y acceso rapido a seguir planificando. -->
    <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-4">
        <div class="itinerary-hero-wrap">
            <h1 class="itinerary-hero-title">
                <span class="itinerary-hero-main">
                    <i class="fas fa-route itinerary-hero-icon"></i>
                    Itinerario -
                </span>
                <span class="itinerary-hero-destination"><?php echo htmlspecialchars($viaje['destino']); ?></span>
            </h1>
            <div class="itinerary-hero-glow"></div>
        </div>
        <?php if ($esOrganizador): ?>
            <a href="servicios.php?viaje_id=<?php echo $viajeId; ?>&categoria=vuelo" class="btn btn-primary">Seguir planificando</a>
        <?php endif; ?>
    </div>

    <!-- Feedback visual de operaciones recientes. -->
    <?php if ($mensaje !== ''): ?><div class="alert alert-success"><?php echo $mensaje; ?></div><?php endif; ?>

    <!-- Selector para cambiar de viaje activo. -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-8">
                    <label class="form-label">Viaje activo</label>
                    <select name="viaje_id" class="form-select">
                        <?php foreach ($misViajes as $itemViaje): ?>
                            <option value="<?php echo (int) $itemViaje['id']; ?>" <?php echo ((int) $itemViaje['id'] === $viajeId) ? 'selected' : ''; ?>>
                                Viaje <?php echo (int) $itemViaje['id']; ?> - <?php echo htmlspecialchars($itemViaje['destino']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <button class="btn btn-outline-primary w-100">Cambiar viaje</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tarjetas resumen del presupuesto del viaje. -->
    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="card h-100"><div class="card-body text-center"><div class="h4 mb-1"><?php echo number_format($presupuesto, 2); ?> EUR</div><small>Presupuesto</small></div></div></div>
        <div class="col-md-3"><div class="card h-100"><div class="card-body text-center"><div class="h4 mb-1"><?php echo number_format($total, 2); ?> EUR</div><small>Gastado</small></div></div></div>
        <div class="col-md-3"><div class="card h-100"><div class="card-body text-center"><div class="h4 mb-1"><?php echo number_format($disponible, 2); ?> EUR</div><small>Disponible</small></div></div></div>
        <div class="col-md-3"><div class="card h-100"><div class="card-body text-center"><div class="h4 mb-1"><?php echo number_format($gastoPersona, 2); ?> EUR</div><small>Por persona</small></div></div></div>
    </div>

    <!-- Barra que representa el porcentaje consumido del presupuesto. -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="small text-muted mb-2"><?php echo $porcentaje; ?>% del presupuesto utilizado</div>
            <div class="progress" style="height: 10px;">
                <div class="progress-bar" role="progressbar" style="width: <?php echo $porcentaje; ?>%"></div>
            </div>
        </div>
    </div>

    <!-- Zona principal con reservas a la izquierda y grupo a la derecha. -->
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">Servicios anadidos (<?php echo count($reservas); ?>)</div>
                <div class="card-body">
                    <?php if (empty($reservas)): ?>
                        <p class="text-muted mb-0">Todavia no hay servicios en este viaje.</p>
                    <?php endif; ?>

                    <?php foreach ($reservas as $reserva): ?>
                        <?php
                        $pagos = obtenerPagosPorReserva((int) $reserva['id']);
                        $miPagoHecho = ((int) ($reserva['usuario_ha_pagado'] ?? 0) === 1);
                        $totalPagado = round((float) ($reserva['total_pagado'] ?? 0), 2);
                        $precioReserva = round((float) ($reserva['precio_aplicado'] ?? 0), 2);
                        $estadoPago = 'Pendiente';
                        if ($totalPagado > 0 && $totalPagado < $precioReserva) {
                            $estadoPago = 'Parcial';
                        } elseif ($totalPagado >= $precioReserva && $precioReserva > 0) {
                            $estadoPago = 'Pagado';
                        }
                        $rutaImagen = './imagenes/' . $reserva['imagen'];
                        $hayImagen = $reserva['imagen'] !== '' && file_exists(__DIR__ . '/imagenes/' . $reserva['imagen']);
                        ?>
                        <div class="border rounded p-3 mb-3">
                            <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
                                <div class="d-flex gap-3 align-items-start">
                                    <?php if ($hayImagen): ?>
                                        <img src="<?php echo htmlspecialchars($rutaImagen); ?>" alt="<?php echo htmlspecialchars($reserva['descripcion']); ?>" class="itinerary-thumb-img">
                                    <?php endif; ?>
                                    <div>
                                        <div class="fw-bold"><?php echo htmlspecialchars($reserva['descripcion']); ?></div>
                                        <div class="small text-muted"><?php echo htmlspecialchars($reserva['tipo']); ?> · <?php echo (int) $reserva['personas']; ?> personas</div>
                                        <div class="small text-muted"><?php echo htmlspecialchars($reserva['detalle'] ?? ''); ?></div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold"><?php echo number_format((float) $reserva['precio_aplicado'], 2); ?> EUR</div>
                                    <span class="badge <?php echo $estadoPago === 'Pagado' ? 'bg-success' : ($estadoPago === 'Parcial' ? 'bg-info text-dark' : 'bg-warning text-dark'); ?>">
                                        <?php echo $estadoPago; ?>
                                    </span>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap mt-3">
                                <div class="small text-muted">
                                    <?php if ($totalPagado <= 0 || empty($pagos)): ?>
                                        Ningun participante ha pagado todavia.
                                    <?php else: ?>
                                        Han pagado: <?php echo htmlspecialchars(implode(' · ', array_map(static fn($pago) => $pago['nombre'], $pagos))); ?>
                                    <?php endif; ?>
                                </div>
                                <div class="d-flex gap-2">
                                    <?php if ($estadoPago !== 'Pagado' && !$miPagoHecho): ?>
                                        <a class="btn btn-sm btn-success" href="pago.php?reserva_id=<?php echo (int) $reserva['id']; ?>&viaje_id=<?php echo (int) $viajeId; ?>">Pagar mi parte</a>
                                    <?php endif; ?>
                                    <?php if ($esOrganizador): ?>
                                        <a class="btn btn-sm btn-outline-danger" href="carrito.php?viaje_id=<?php echo (int) $viajeId; ?>&eliminar_reserva=<?php echo (int) $reserva['id']; ?>">Eliminar</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">Grupo del viaje</div>
                <div class="card-body">
                    <?php if ($esOrganizador): ?>
                        <form method="POST" action="carrito.php?viaje_id=<?php echo (int) $viajeId; ?>" class="d-flex gap-2 mb-3">
                            <input type="hidden" name="viaje_id" value="<?php echo $viajeId; ?>">
                            <input type="hidden" name="accion" value="invitar">
                            <input type="email" class="form-control" name="invitar_email" placeholder="Email del participante" required>
                            <button class="btn btn-primary">Invitar</button>
                        </form>
                    <?php endif; ?>
                    <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                        <span><?php echo htmlspecialchars($organizador['nombre'] ?? 'Organizador'); ?></span>
                        <span class="badge bg-primary">Organizador</span>
                    </div>
                    <?php foreach ($participantes as $participante): ?>
                        <?php
                        $estado = $participante['estado_invitacion'] ?? 'pendiente';
                        $clase = $estado === 'aceptada' ? 'bg-success' : ($estado === 'rechazada' ? 'bg-secondary' : 'bg-warning text-dark');
                        ?>
                        <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                            <span><?php echo htmlspecialchars($participante['nombre']); ?></span>
                            <span class="badge <?php echo $clase; ?>"><?php echo ucfirst($estado); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
