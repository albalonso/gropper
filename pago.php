<?php
// Inicia la sesion para conocer el usuario que realiza la simulacion de pago.
session_start();
// Carga la utilidad de limpieza de entradas.
require_once __DIR__ . "/database/securizar.php";
// Carga las funciones de viajes, reservas y pagos.
require_once __DIR__ . "/database/funcionesDB.php";

// Solo usuarios autenticados pueden acceder a esta pantalla.
if (!asegurarUsuarioSesion()) {
    header("Location: login.php");
    exit();
}

// Recupera ids de reserva y viaje desde GET o POST.
$reservaId = (int) ($_GET['reserva_id'] ?? $_POST['reserva_id'] ?? 0);
$viajeId = (int) ($_GET['viaje_id'] ?? $_POST['viaje_id'] ?? 0);
$reserva = obtenerReservaPorId($reservaId);
$viaje = null;
$error = "";

// Si hay reserva directa, se carga su viaje asociado.
if ($reserva) {
    $viajeId = (int) ($reserva['viaje_id'] ?? $viajeId);
    $viaje = obtenerViaje($viajeId);
} elseif ($viajeId > 0) {
    $reservasViaje = obtenerReservasViajeParaUsuario($viajeId, (int) $_SESSION['usuario_id']);
    foreach ($reservasViaje as $reservaCandidata) {
        $precioCandidato = (float) ($reservaCandidata['precio_aplicado'] ?? 0);
        $pagadoCandidato = (float) ($reservaCandidata['total_pagado'] ?? 0);
        $yaPagoCandidato = ((int) ($reservaCandidata['usuario_ha_pagado'] ?? 0) === 1);
        if ($pagadoCandidato < $precioCandidato && !$yaPagoCandidato) {
            $reserva = $reservaCandidata;
            $reservaId = (int) ($reserva['id'] ?? 0);
            break;
        }
    }
    $viaje = obtenerViaje($viajeId);
}

// Si no existe una reserva valida, prepara un error para la vista.
if (!$reserva || !$viaje) {
    $error = "No se ha encontrado la reserva que intentas pagar.";
}

$esOrganizadorViaje = $viaje && (int) ($viaje['organizador_id'] ?? 0) === (int) $_SESSION['usuario_id'];
$esParticipanteAceptado = false;
if ($viaje) {
    foreach (obtenerParticipantesViaje($viajeId) as $participante) {
        if ((int) ($participante['id'] ?? 0) === (int) $_SESSION['usuario_id'] && ($participante['estado_invitacion'] ?? '') === 'aceptada') {
            $esParticipanteAceptado = true;
            break;
        }
    }
}

if ($viaje && !$esOrganizadorViaje && !$esParticipanteAceptado) {
    $error = "No tienes permiso para pagar esta reserva.";
}

// Calcula importes y estado de pago del usuario actual.
$ok = false;
$precioTotal = (float) ($reserva['precio_aplicado'] ?? 0);
$personas = (int)($reserva['personas'] ?? 1);
$parte = calcularParteReserva($precioTotal, $personas);
$pagadoActual = (float)($reserva['total_pagado'] ?? 0);
$pendiente = max(0, round($precioTotal - $pagadoActual, 2));
$miParte = min($parte, $pendiente);
$yaPague = false;
if ($reserva) {
    foreach (obtenerPagosPorReserva($reservaId) as $pagoExistente) {
        if ((int) ($pagoExistente['usuario_id'] ?? 0) === (int) $_SESSION['usuario_id']) {
            $yaPague = true;
            break;
        }
    }
}

// Procesa el formulario de simulacion de pago.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $error === "") {
    $titular = securizar($_POST['nombre_titular'] ?? "");
    $tarjeta = preg_replace('/\s+/', '', securizar($_POST['tarjeta'] ?? ""));
    $fecha = securizar($_POST['fecha_exp'] ?? "");

    if ($yaPague) {
        $error = "Ya habias pagado tu parte de este servicio.";
    } elseif ($pendiente <= 0) {
        $error = "Este servicio ya esta completamente pagado.";
    } elseif ($titular === "" || strlen($tarjeta) < 16 || $fecha === "") {
        $error = "Completa correctamente los datos de pago.";
    } else {
        $ok = registrarPagoParteReserva($reservaId, (int) $_SESSION['usuario_id']);
        if (!$ok) {
            $error = "No se pudo registrar el pago de tu parte (quizas ya has pagado o el servicio ya esta completo).";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Metadatos y estilos de la pantalla de simulacion de pago. -->
    <meta charset="UTF-8">
    <title>Simulacion de Pago - Gropper</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./views/estilo.css?v=20260407b">
</head>
<body>
<?php /* Menu comun superior. */ ?>
<?php include_once "./views/menu.php"; ?>
<div class="container mt-4 mb-5">
    <!-- Aviso fijo indicando que no deben usarse datos reales. -->
    <div class="alert alert-warning border-warning-subtle">
        <strong>ENTORNO DE SIMULACION</strong> - No introduzcas datos de tarjeta reales.
    </div>

    <!-- Si el pago se registro correctamente, se muestra la confirmacion final. -->
    <?php if ($ok): ?>
        <div class="card">
            <div class="card-body p-4">
                    <div class="alert alert-success mb-3">Pago simulado realizado con exito. Tu parte ha quedado registrada.</div>
                <a class="btn btn-success" href="carrito.php?viaje_id=<?php echo $viajeId; ?>">Volver al itinerario</a>
            </div>
        </div>
    <!-- En caso contrario se muestra el formulario y el resumen del pago. -->
    <?php else: ?>
    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">Datos de pago</div>
                <div class="card-body p-4">
                    <?php if ($error !== ""): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>

                    <div class="rounded-4 p-4 mb-3 text-white" style="background: linear-gradient(135deg,#8b8f97,#6f7683);">
                        <div class="d-flex justify-content-between small opacity-75 mb-4">
                            <span>Tarjeta simulada</span><span>VISA</span>
                        </div>
                        <div class="fs-5 fw-semibold mb-3">**** **** **** ****</div>
                        <div class="d-flex justify-content-between small">
                            <span>NOMBRE TITULAR</span><span>MM / AA</span>
                        </div>
                    </div>

                    <form method="POST">
                        <input type="hidden" name="reserva_id" value="<?php echo (int) $reservaId; ?>">
                        <input type="hidden" name="viaje_id" value="<?php echo (int) $viajeId; ?>">
                        <div class="row g-2 mb-3">
                            <div class="col-md-4"><button type="button" class="btn btn-outline-secondary w-100">VISA</button></div>
                            <div class="col-md-4"><button type="button" class="btn btn-outline-secondary w-100">Mastercard</button></div>
                            <div class="col-md-4"><button type="button" class="btn btn-outline-secondary w-100">PayPal</button></div>
                        </div>

                        <label class="form-label">Nombre del titular</label>
                        <input class="form-control mb-2" name="nombre_titular" placeholder="No usar datos reales" required>

                        <label class="form-label">Numero de tarjeta</label>
                        <input class="form-control mb-2" name="tarjeta" placeholder="**** **** **** ****" required>

                        <div class="row g-2">
                            <div class="col-md-4">
                                <label class="form-label">Caducidad</label>
                                <input class="form-control" name="fecha_exp" placeholder="MM/AA" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">CVV</label>
                                <input class="form-control" placeholder="***">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Pais</label>
                                <input class="form-control" value="Espana" readonly>
                            </div>
                        </div>

                        <div class="alert alert-warning small mt-3 mb-3">Entorno de simulacion: ningun cargo real sera procesado.</div>

                        <?php if ($yaPague): ?>
                            <div class="alert alert-info mb-3">Ya habias pagado tu parte de este servicio.</div>
                        <?php endif; ?>
                        <button class="btn btn-warning btn-lg w-100 fw-bold" <?php echo ($yaPague || $pendiente <= 0) ? 'disabled' : ''; ?>>
                            Simular pago de tu parte: <?php echo number_format($miParte, 2); ?> EUR
                        </button>
                        <div class="text-center mt-3">
                            <a class="text-muted small text-decoration-none" href="carrito.php?viaje_id=<?php echo $viajeId; ?>">Cancelar y volver al itinerario</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-header">Resumen del pago</div>
                <div class="card-body">
                    <div class="p-3 bg-light rounded mb-3">
                        <div class="fw-bold">Viaje <?php echo (int)$viajeId; ?> - <?php echo htmlspecialchars((string) ($viaje['destino'] ?? 'Destino')); ?></div>
                        <div class="small text-muted"><?php echo htmlspecialchars((string) ($viaje['fecha_inicio'] ?? '')); ?> - <?php echo htmlspecialchars((string) ($viaje['fecha_fin'] ?? '')); ?> · <?php echo (int)($reserva['personas'] ?? 1); ?> personas</div>
                    </div>
                    <div class="border rounded p-2 mb-3">
                        <div class="fw-semibold"><?php echo htmlspecialchars((string) ($reserva['descripcion'] ?? 'Servicio')); ?></div>
                        <div class="small text-muted"><?php echo $pendiente <= 0 ? 'Pagado' : 'Pendiente'; ?></div>
                    </div>
                    <div class="d-flex justify-content-between small"><span>Subtotal servicio</span><span><?php echo number_format((float) ($reserva['precio_aplicado'] ?? 0), 2); ?> EUR</span></div>
                    <div class="d-flex justify-content-between small"><span>Pagado acumulado</span><span><?php echo number_format($pagadoActual, 2); ?> EUR</span></div>
                    <div class="d-flex justify-content-between small"><span>Pendiente</span><span><?php echo number_format($pendiente, 2); ?> EUR</span></div>
                    <div class="d-flex justify-content-between small"><span>Tu parte</span><span><?php echo number_format($miParte, 2); ?> EUR</span></div>
                    <div class="d-flex justify-content-between small"><span>Tasas y cargos</span><span>0,00 EUR</span></div>
                    <hr>
                    <div class="d-flex justify-content-between fw-bold fs-5"><span>Total simulado</span><span><?php echo number_format($miParte, 2); ?> EUR</span></div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="fw-semibold mb-2">Que pasa tras simular?</div>
                    <ol class="small text-muted ps-3 mb-0">
                        <li>El servicio se marca como pagado.</li>
                        <li>Se guarda el registro del pago.</li>
                        <li>No se realiza ningun cobro real.</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
</body>
</html>

?>