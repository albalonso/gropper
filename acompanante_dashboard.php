<?php
// Inicia la sesion del acompanante autenticado.
session_start();
// Carga las funciones de invitaciones y viajes.
require_once __DIR__ . "/database/funcionesDB.php";

// Restringe esta pantalla a usuarios con rol de acompanante.
if (!asegurarUsuarioSesion() || ($_SESSION['usuario_rol'] ?? '') !== 'acompanante') {
    header("Location: dashboard.php");
    exit();
}

// Guarda el id del acompanante actual para consultar sus datos.
$usuarioId = (int) $_SESSION['usuario_id'];
// Variable de mensaje para feedback de aceptacion o rechazo de invitaciones.
$msg = "";

// Convierte una fecha larga en un formato corto mas legible para las tarjetas.
function formatoFechaCorta(?string $fecha): string
{
    if (!$fecha) {
        return "";
    }
    $ts = strtotime($fecha);
    if (!$ts) {
        return "";
    }
    $meses = ['ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic'];
    $mes = $meses[(int)date('n', $ts) - 1];
    return date('j', $ts) . " " . $mes . " " . date('Y', $ts);
}

// Procesa la respuesta del acompanante a una invitacion concreta.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['trip_id'], $_POST['accion'])) {
    $tripId = (int) $_POST['trip_id'];
    if ($_POST['accion'] === 'aceptar') {
        responderInvitacion($usuarioId, $tripId, 'aceptada');
        $msg = "Has aceptado la invitacion. El viaje ahora aparece en tu agenda.";
    } elseif ($_POST['accion'] === 'rechazar') {
        responderInvitacion($usuarioId, $tripId, 'rechazada');
        $msg = "Has rechazado la invitacion.";
    }
}

// Carga viajes pendientes y viajes ya aceptados para mostrarlos en pantalla.
$pendientes = obtenerInvitacionesPendientes($usuarioId);
$aceptadas = obtenerViajesAceptados($usuarioId);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Metadatos y estilos del dashboard de acompanante. -->
    <meta charset="UTF-8">
    <title>Dashboard Acompanante - Gropper</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./views/estilo.css?v=20260407b">
</head>
<body>
<?php /* Menu comun superior. */ ?>
<?php include_once "./views/menu.php"; ?>
<div class="container mt-4 mb-5">
    <!-- Mensaje de resultado tras responder una invitacion. -->
    <?php if ($msg !== ""): ?><div class="alert alert-success"><?php echo $msg; ?></div><?php endif; ?>
    <!-- Explicacion del modo de funcionamiento para el acompanante. -->
    <div class="alert alert-primary border-primary-subtle">
        Estas en modo Acompanante: puedes ver invitaciones, aceptar/rechazar y pagar servicios de tus viajes confirmados.
    </div>

    <!-- Seccion de invitaciones pendientes. -->
    <div class="text-center mb-4 mt-4">
        <h2 class="section-title">
            <span class="title-main">Mis invitaciones</span>
            <span class="title-accent">pendientes</span>
        </h2>
        <div class="title-decoration"></div>
        <small class="text-muted"><?php echo count($pendientes); ?> sin responder</small>
    </div>
    <!-- Tarjetas de invitaciones pendientes. -->
    <div class="row g-3 mb-4">
        <?php if (empty($pendientes)): ?>
            <div class="col-12">
                <div class="card p-4 text-center text-muted">No tienes invitaciones pendientes por ahora.</div>
            </div>
        <?php else: ?>
            <?php foreach ($pendientes as $trip): ?>
                <div class="col-lg-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <img src="./imagenes/<?php echo htmlspecialchars(getImagenDestino($trip['destino'])); ?>" class="service-thumb w-100 mb-3" style="height: 150px; object-fit: cover;" alt="<?php echo htmlspecialchars($trip['destino']); ?>">
                            <h5 class="mb-1">Viaje <?php echo (int)$trip['id']; ?> - <?php echo htmlspecialchars($trip['destino']); ?></h5>
                            <p class="small text-muted mb-1">Organiza: <?php echo htmlspecialchars($trip['organizador_nombre'] ?? 'Organizador'); ?></p>
                            <p class="small text-muted mb-2">
                                <?php echo formatoFechaCorta($trip['fecha_inicio'] ?? ''); ?> — <?php echo formatoFechaCorta($trip['fecha_fin'] ?? ''); ?>
                                · <?php echo (int)($trip['personas_confirmadas'] ?? 1); ?> personas
                            </p>
                            <form method="POST" class="d-flex gap-2">
                                <input type="hidden" name="trip_id" value="<?php echo (int)$trip['id']; ?>">
                                <button class="btn btn-success btn-sm w-50" name="accion" value="aceptar">Aceptar</button>
                                <button class="btn btn-outline-danger btn-sm w-50" name="accion" value="rechazar">Rechazar</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Seccion de viajes ya aceptados. -->
    <div class="text-center mb-4 mt-5">
        <h2 class="section-title">
            <span class="title-main">Mis</span>
            <span class="title-accent">proximos viajes</span>
        </h2>
        <div class="title-decoration"></div>
        <small class="text-muted"><?php echo count($aceptadas); ?> aceptado<?php echo count($aceptadas) === 1 ? '' : 's'; ?></small>
    </div>
    <!-- Tarjetas de viajes confirmados. -->
    <div class="row g-3">
        <?php if (empty($aceptadas)): ?>
            <div class="col-12">
                <div class="card p-4 text-center text-muted">Aqui apareceran tus viajes aceptados.</div>
            </div>
        <?php else: ?>
            <?php foreach ($aceptadas as $trip): ?>
                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <img src="./imagenes/<?php echo htmlspecialchars(getImagenDestino($trip['destino'])); ?>" class="service-thumb w-100 mb-3" style="height: 150px; object-fit: cover;" alt="<?php echo htmlspecialchars($trip['destino']); ?>">
                            <h5 class="mb-1">Viaje <?php echo (int)$trip['id']; ?> - <?php echo htmlspecialchars($trip['destino']); ?></h5>
                            <p class="small text-muted mb-1">Organiza: <?php echo htmlspecialchars($trip['organizador_nombre'] ?? 'Organizador'); ?></p>
                            <p class="small text-muted mb-3">
                                <?php echo formatoFechaCorta($trip['fecha_inicio'] ?? ''); ?> — <?php echo formatoFechaCorta($trip['fecha_fin'] ?? ''); ?>
                                · <?php echo (int)($trip['personas_confirmadas'] ?? 1); ?> personas
                            </p>
                            <a class="btn btn-primary btn-sm" href="carrito.php?viaje_id=<?php echo (int)$trip['id']; ?>">Ver itinerario</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
