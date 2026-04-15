<?php
// Inicia la sesion para saber que usuario y que viaje estan en contexto.
session_start();
// Carga las funciones de negocio relacionadas con servicios y reservas.
require_once __DIR__ . '/database/funcionesDB.php';

// Solo usuarios autenticados pueden entrar al buscador de servicios.
if (!asegurarUsuarioSesion()) {
    header('Location: login.php');
    exit();
}

// Datos basicos del usuario autenticado.
$usuarioId = (int) ($_SESSION['usuario_id'] ?? 0);
$esOrganizador = (($_SESSION['usuario_rol'] ?? '') === 'organizador');

// Categoria seleccionada y categorias permitidas dentro del sistema.
$categoria = $_GET['categoria'] ?? 'vuelo';
$categoriasPermitidas = ['vuelo', 'alojamiento', 'actividad'];
if (!in_array($categoria, $categoriasPermitidas, true)) {
    $categoria = 'vuelo';
}

$viajeId = (int) ($_GET['viaje_id'] ?? ($_SESSION['viaje_activo_id'] ?? 0));
$personas = max(1, (int) ($_GET['personas'] ?? $_POST['personas'] ?? 1));
$mensaje = '';
$error = '';

// Sin viaje activo no tiene sentido mostrar servicios.
if ($viajeId <= 0) {
    header('Location: dashboard.php');
    exit();
}

$viaje = obtenerViaje($viajeId);
if (!$viaje) {
    header('Location: dashboard.php');
    exit();
}

// Solo el organizador propietario del viaje puede anadir servicios.
if (!$esOrganizador || (int) ($viaje['organizador_id'] ?? 0) !== $usuarioId) {
    header('Location: carrito.php?viaje_id=' . $viajeId);
    exit();
}

$entrada = (string) ($viaje['fecha_inicio'] ?? '');
$salida = (string) ($viaje['fecha_fin'] ?? '');
$fechaActividad = (string) ($_GET['fecha_actividad'] ?? $entrada);
$_SESSION['viaje_activo_id'] = $viajeId;

// Procesa la accion de anadir un servicio al viaje.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_id'])) {
    $resultado = anadirServicioAViaje($viajeId, (int) $_POST['add_id'], $personas);
    if ($resultado['ok']) {
        $mensaje = $resultado['msg'];
    } else {
        $error = $resultado['msg'];
    }
}

// Carga servicios, reservas ya existentes y metricas de presupuesto.
$servicios = obtenerServicios($categoria, $viaje['destino']);
$reservas = obtenerReservasViaje($viajeId);
$serviciosReservados = [];
foreach ($reservas as $reserva) {
    $serviciosReservados[(int) $reserva['servicio_id']] = true;
}

$presupuesto = (float) ($viaje['presupuesto_limite'] ?? 0);
$total = getTotalViaje($viajeId);
$restante = round($presupuesto - $total, 2);
$destinoVuelo = getAeropuertoDestino($viaje['destino']);

$noches = 1;
if ($categoria === 'alojamiento' && $entrada !== '' && $salida !== '') {
    $inicioTs = strtotime($entrada);
    $finTs = strtotime($salida);
    if ($inicioTs && $finTs && $finTs > $inicioTs) {
        $noches = max(1, (int) round(($finTs - $inicioTs) / 86400));
    }
}

// Traduce la categoria tecnica a un titulo mas legible para la vista.
function titulo_categoria(string $categoria): string
{
    return [
        'vuelo' => 'Gestion de vuelo',
        'alojamiento' => 'Gestion de alojamiento',
        'actividad' => 'Gestion de actividad',
    ][$categoria] ?? 'Servicios';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Metadatos y estilos de la pagina de servicios. -->
    <meta charset="UTF-8">
    <title>Servicios - Gropper</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./views/estilo.css?v=20260409a">
</head>
<body>
<?php /* Menu comun superior. */ ?>
<?php include_once './views/menu.php'; ?>
<div class="container mt-4 mb-5">
    <!-- Titulo y resumen economico del viaje. -->
    <div class="mb-3">
        <h1 class="services-page-title"><?php echo titulo_categoria($categoria); ?> - Viaje <?php echo (int) $viaje['id']; ?></h1>
        <p class="services-page-subtitle">Destino: <?php echo htmlspecialchars($viaje['destino']); ?> | Presupuesto: <?php echo number_format($presupuesto, 2); ?> EUR | Planificado: <?php echo number_format($total, 2); ?> EUR</p>
    </div>

    <!-- Barra superior con navegacion rapida y presupuesto restante. -->
    <div class="card mb-3 services-toolbar-card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap">
                <a href="gestionar_viajes.php" class="btn btn-outline-primary">Volver al Dashboard</a>
                <a href="carrito.php?viaje_id=<?php echo $viajeId; ?>" class="btn btn-outline-primary">Ver mi itinerario</a>
                <span class="budget-chip">Presupuesto restante: <?php echo number_format($restante, 2); ?> EUR</span>
            </div>
        </div>
    </div>

    <!-- Formulario de filtros y parametros segun la categoria elegida. -->
    <div class="card mb-4 services-search-card">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <input type="hidden" name="viaje_id" value="<?php echo $viajeId; ?>">
                <input type="hidden" name="categoria" value="<?php echo htmlspecialchars($categoria); ?>">

                <?php if ($categoria === 'alojamiento'): ?>
                    <div class="col-md-3">
                        <label class="form-label">Ciudad / Zona</label>
                        <input class="form-control" value="<?php echo htmlspecialchars($viaje['destino']); ?>" readonly>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Entrada</label>
                        <input type="date" name="entrada" class="form-control" value="<?php echo htmlspecialchars($entrada); ?>" readonly>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Salida</label>
                        <input type="date" name="salida" class="form-control" value="<?php echo htmlspecialchars($salida); ?>" readonly>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">Personas</label>
                        <input type="number" min="1" max="12" name="personas" class="form-control" value="<?php echo $personas; ?>">
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-primary w-100">Buscar alojamiento</button>
                    </div>
                <?php elseif ($categoria === 'vuelo'): ?>
                    <div class="col-md-2">
                        <label class="form-label">Origen</label>
                        <input class="form-control" value="Madrid (MAD)" readonly>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Destino</label>
                        <input class="form-control" value="<?php echo htmlspecialchars($destinoVuelo); ?>" readonly>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Ida</label>
                        <input type="date" name="entrada" class="form-control" value="<?php echo htmlspecialchars($entrada); ?>" readonly>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Vuelta</label>
                        <input type="date" name="salida" class="form-control" value="<?php echo htmlspecialchars($salida); ?>" readonly>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">Personas</label>
                        <input type="number" min="1" max="12" name="personas" class="form-control" value="<?php echo $personas; ?>">
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary w-100">Buscar vuelos</button>
                    </div>
                <?php elseif ($categoria === 'actividad'): ?>
                    <div class="col-md-4">
                        <label class="form-label">Destino</label>
                        <input class="form-control" value="<?php echo htmlspecialchars($viaje['destino']); ?>" readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Fecha actividad</label>
                        <input type="date" name="fecha_actividad" class="form-control" value="<?php echo htmlspecialchars($fechaActividad); ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Personas</label>
                        <input type="number" min="1" max="12" name="personas" class="form-control" value="<?php echo $personas; ?>">
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary w-100">Buscar actividades</button>
                    </div>
                <?php else: ?>
                    <div class="col-md-4">
                        <label class="form-label">Destino</label>
                        <input class="form-control" value="<?php echo htmlspecialchars($viaje['destino']); ?>" readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Fechas</label>
                        <input class="form-control" value="<?php echo htmlspecialchars($entrada . ' - ' . $salida); ?>" readonly>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Personas</label>
                        <input type="number" min="1" max="12" name="personas" class="form-control" value="<?php echo $personas; ?>">
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary w-100">Buscar</button>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- Feedback de operaciones de alta de servicios. -->
    <?php if ($mensaje !== ''): ?><div class="alert alert-success"><?php echo $mensaje; ?></div><?php endif; ?>
    <?php if ($error !== ''): ?><div class="alert alert-warning"><?php echo $error; ?></div><?php endif; ?>

    <p class="services-count"><?php echo count($servicios); ?> resultados encontrados</p>

    <!-- Vista horizontal especifica para vuelos. -->
    <?php if ($categoria === 'vuelo'): ?>
        <div class="services-list">
            <?php foreach ($servicios as $servicio): ?>
                <?php
                $precio = round((float) $servicio['precio_total'] * $personas, 2);
                $yaReservado = isset($serviciosReservados[(int) $servicio['id']]);
                $bloqueado = $precio > $restante;
                $rutaImagen = './imagenes/' . $servicio['imagen'];
                $hayImagen = $servicio['imagen'] !== '' && file_exists(__DIR__ . '/imagenes/' . $servicio['imagen']);
                ?>
                <div class="card flight-card mb-4">
                    <div class="card-body">
                        <div class="flight-card-grid">
                            <div class="flight-company-col">
                                <?php if ($hayImagen): ?>
                                    <div class="flight-logo-box"><img src="<?php echo htmlspecialchars($rutaImagen); ?>" alt="<?php echo htmlspecialchars($servicio['airline'] ?? $servicio['descripcion']); ?>" class="flight-logo-img"></div>
                                <?php else: ?>
                                    <div class="flight-logo-box"><?php echo htmlspecialchars(substr((string) ($servicio['airline'] ?? 'AV'), 0, 2)); ?></div>
                                <?php endif; ?>
                                <div class="flight-company-name"><?php echo htmlspecialchars($servicio['airline'] ?? 'Compania'); ?></div>
                            </div>
                            <div class="flight-times-col">
                                <div class="flight-time-row">
                                    <div class="flight-hours"><?php echo htmlspecialchars($servicio['ida_out'] ?? '08:00'); ?> -> <?php echo htmlspecialchars($servicio['ida_in'] ?? '10:00'); ?></div>
                                    <div class="flight-route">Madrid (MAD) - <?php echo htmlspecialchars($destinoVuelo); ?></div>
                                </div>
                                <div class="flight-time-row">
                                    <div class="flight-hours"><?php echo htmlspecialchars($servicio['vuelta_out'] ?? '18:00'); ?> -> <?php echo htmlspecialchars($servicio['vuelta_in'] ?? '20:00'); ?></div>
                                    <div class="flight-route"><?php echo htmlspecialchars($destinoVuelo); ?> - Madrid (MAD)</div>
                                </div>
                            </div>
                            <div class="flight-meta-col">
                                <div><?php echo htmlspecialchars($servicio['duracion'] ?? ''); ?></div>
                                <div><?php echo htmlspecialchars($servicio['escalas'] ?? ''); ?></div>
                            </div>
                            <div class="flight-price-col">
                                <div class="flight-price-amount"><?php echo number_format($precio, 2); ?> EUR</div>
                                <div class="flight-price-detail"><?php echo number_format((float) $servicio['precio_total'], 2); ?> / persona</div>
                            </div>
                            <div class="flight-action-col">
                                <form method="POST">
                                    <input type="hidden" name="add_id" value="<?php echo (int) $servicio['id']; ?>">
                                    <input type="hidden" name="personas" value="<?php echo $personas; ?>">
                                    <button type="submit" class="btn <?php echo ($yaReservado || $bloqueado) ? 'btn-outline-secondary' : 'btn-success'; ?> w-100 flight-book-btn" <?php echo ($yaReservado || $bloqueado) ? 'disabled' : ''; ?>>
                                        <?php echo $yaReservado ? 'Ya reservado' : ($bloqueado ? 'Presupuesto insuficiente' : 'Anadir al viaje'); ?>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if ($yaReservado): ?>
                    <p class="service-state-note success">Este servicio ya esta reservado en el viaje.</p>
                <?php elseif ($bloqueado): ?>
                    <p class="service-state-note warning">Este servicio supera el presupuesto restante.</p>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <!-- Vista en rejilla para alojamiento, actividades y categorias genericas. -->
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($servicios as $servicio): ?>
                <?php
                $precio = round((float) $servicio['precio_total'] * $personas, 2);
                $yaReservado = isset($serviciosReservados[(int) $servicio['id']]);
                $bloqueado = $precio > $restante;
                $rutaImagen = './imagenes/' . $servicio['imagen'];
                $hayImagen = $servicio['imagen'] !== '' && file_exists(__DIR__ . '/imagenes/' . $servicio['imagen']);
                ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 service-grid-card <?php echo $categoria === 'alojamiento' ? 'hotel-card' : ''; ?>">
                        <?php if ($hayImagen): ?>
                            <img src="<?php echo htmlspecialchars($rutaImagen); ?>" class="card-img-top service-grid-img" alt="<?php echo htmlspecialchars($servicio['descripcion']); ?>">
                        <?php else: ?>
                            <div class="card-img-top service-grid-placeholder d-flex align-items-center justify-content-center bg-light">Sin imagen</div>
                        <?php endif; ?>
                        <div class="card-body d-flex flex-column">
                            <?php if ($categoria === 'alojamiento'): ?>
                                <h5 class="hotel-title"><?php echo htmlspecialchars($servicio['descripcion']); ?></h5>
                                <div class="hotel-stars"><?php echo str_repeat('★', (int) ($servicio['estrellas'] ?? 3)); ?></div>
                                <div class="hotel-zone"><?php echo htmlspecialchars($servicio['zona'] ?? $viaje['destino']); ?></div>
                                <div class="hotel-features"><?php echo htmlspecialchars($servicio['servicios_hotel'] ?? 'WiFi'); ?></div>
                                <div class="hotel-rating">
                                    <span class="hotel-rating-score"><?php echo number_format((float) ($servicio['puntuacion'] ?? 8.0), 1); ?></span>
                                    <span class="hotel-rating-reviews"><?php echo (int) ($servicio['reviews'] ?? 100); ?> reseñas</span>
                                </div>
                                <div class="mt-auto">
                                    <div class="hotel-price"><?php echo number_format($precio, 2); ?> EUR</div>
                                    <div class="hotel-price-detail"><?php echo number_format((float) $servicio['precio_total'], 2); ?> EUR / persona · <?php echo $noches; ?> noches</div>
                                    <form method="POST" class="mt-3">
                                        <input type="hidden" name="add_id" value="<?php echo (int) $servicio['id']; ?>">
                                        <input type="hidden" name="personas" value="<?php echo $personas; ?>">
                                        <button type="submit" class="btn <?php echo ($yaReservado || $bloqueado) ? 'btn-outline-secondary' : 'btn-success'; ?> w-100 hotel-book-btn" <?php echo ($yaReservado || $bloqueado) ? 'disabled' : ''; ?>>
                                            <?php echo $yaReservado ? 'Ya reservado' : ($bloqueado ? 'Presupuesto insuficiente' : 'Reservar para el grupo'); ?>
                                        </button>
                                    </form>
                                </div>
                            <?php elseif ($categoria === 'actividad'): ?>
                                <div class="activity-image-wrap">
                                    <?php if (!empty($servicio['badge'])): ?>
                                        <span class="activity-badge"><?php echo htmlspecialchars($servicio['badge']); ?></span>
                                    <?php endif; ?>
                                    <span class="activity-fav">♡</span>
                                </div>
                                <h5 class="activity-title"><?php echo htmlspecialchars($viaje['destino'] . ': ' . $servicio['descripcion']); ?></h5>
                                <div class="activity-meta-line"><?php echo htmlspecialchars($servicio['duracion'] ?? '3 horas'); ?> · <?php echo htmlspecialchars($servicio['extras'] ?? 'Grupo reducido'); ?></div>
                                <div class="activity-footer mt-auto">
                                    <div class="activity-rating-line">
                                        <span class="activity-rating-number"><?php echo number_format((float) ($servicio['rating'] ?? 4.8), 1); ?></span>
                                        <span class="activity-rating-star">★</span>
                                        <span class="activity-rating-reviews">(<?php echo (int) ($servicio['reviews'] ?? 100); ?>)</span>
                                    </div>
                                    <div class="activity-price-line">
                                        <span class="activity-old-price">Desde <?php echo number_format((float) ($servicio['old_price'] ?? ($servicio['precio_total'] + 8)), 0); ?> EUR</span>
                                        <span class="activity-new-price"><?php echo number_format($precio, 0); ?> EUR</span>
                                    </div>
                                    <div class="activity-date-note">Fecha elegida: <?php echo htmlspecialchars($fechaActividad); ?></div>
                                    <form method="POST" class="mt-3">
                                        <input type="hidden" name="add_id" value="<?php echo (int) $servicio['id']; ?>">
                                        <input type="hidden" name="personas" value="<?php echo $personas; ?>">
                                        <button type="submit" class="btn <?php echo ($yaReservado || $bloqueado) ? 'btn-outline-secondary' : 'btn-success'; ?> w-100 hotel-book-btn" <?php echo ($yaReservado || $bloqueado) ? 'disabled' : ''; ?>>
                                            <?php echo $yaReservado ? 'Ya reservado' : ($bloqueado ? 'Presupuesto insuficiente' : 'Anadir al viaje'); ?>
                                        </button>
                                    </form>
                                </div>
                            <?php else: ?>
                                <div class="small text-uppercase text-muted mb-2"><?php echo htmlspecialchars($servicio['tipo']); ?></div>
                                <h5 class="card-title"><?php echo htmlspecialchars($servicio['descripcion']); ?></h5>
                                <p class="text-muted small mb-3"><?php echo htmlspecialchars($servicio['detalle'] ?? ''); ?></p>
                                <div class="mt-auto">
                                    <div class="fw-bold fs-5"><?php echo number_format($precio, 2); ?> EUR</div>
                                    <div class="small text-muted mb-3"><?php echo number_format((float) $servicio['precio_total'], 2); ?> EUR por persona</div>
                                    <form method="POST">
                                        <input type="hidden" name="add_id" value="<?php echo (int) $servicio['id']; ?>">
                                        <input type="hidden" name="personas" value="<?php echo $personas; ?>">
                                        <button type="submit" class="btn <?php echo ($yaReservado || $bloqueado) ? 'btn-outline-secondary' : 'btn-success'; ?> w-100" <?php echo ($yaReservado || $bloqueado) ? 'disabled' : ''; ?>>
                                            <?php echo $yaReservado ? 'Ya anadido' : ($bloqueado ? 'Presupuesto insuficiente' : 'Anadir al viaje'); ?>
                                        </button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
