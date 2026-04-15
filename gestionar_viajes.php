<?php
// Inicia la sesion para trabajar con el organizador autenticado.
session_start();
// Carga la utilidad para sanear entradas del formulario.
require_once __DIR__ . "/database/securizar.php";
// Carga todas las funciones de negocio relacionadas con viajes y servicios.
require_once __DIR__ . "/database/funcionesDB.php";

// Restringe esta pantalla unicamente a usuarios con rol de organizador.
if (!asegurarUsuarioSesion() || ($_SESSION["usuario_rol"] ?? '') !== "organizador") {
    header("Location: login.php");
    exit();
}

// Recupera el id del organizador desde la sesion.
$usuarioId = (int) $_SESSION["usuario_id"];
// Variable de feedback para mostrar mensajes de exito o error.
$mensaje = "";
// Recupera el viaje actualmente activo si existe en la sesion.
$viajeActivoId = (int) ($_SESSION['viaje_activo_id'] ?? 0);

// Procesa la creacion de un nuevo viaje enviada por formulario.
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["crear_viaje"])) {
    $destino = securizar($_POST["destino"] ?? "");
    $presupuesto = (float) ($_POST["presupuesto"] ?? 0);
    $fechaInicio = securizar($_POST["fecha_inicio"] ?? "");
    $fechaFin = securizar($_POST["fecha_fin"] ?? "");

    if ($destino !== "" && $presupuesto > 0) {
        $resultado = crearViaje($usuarioId, $destino, $presupuesto, $fechaInicio, $fechaFin);
        $mensaje = $resultado['msg'];
        if (($resultado['viaje_id'] ?? 0) > 0) {
            $_SESSION['viaje_activo_id'] = (int) $resultado['viaje_id'];
            $viajeActivoId = (int) $resultado['viaje_id'];
        }
    }
}

// Procesa el cambio de viaje activo usando el selector.
if (isset($_GET['activar'])) {
    $activar = (int) $_GET['activar'];
    if (obtenerViaje($activar)) {
        $_SESSION['viaje_activo_id'] = $activar;
    }
}

// Procesa el borrado de un viaje del organizador actual.
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["borrar_viaje_id"])) {
    $viajeId = (int) $_POST["borrar_viaje_id"];
    $resultado = borrarViaje($viajeId, $usuarioId);
    $mensaje = $resultado['msg'];
    
    // Si se borra el viaje activo, limpiar la sesión
    if ($viajeId === $viajeActivoId) {
        unset($_SESSION['viaje_activo_id']);
        $viajeActivoId = 0;
    }
    
    // Recargar la lista de viajes
    $misViajes = obtenerViajesOrganizador($usuarioId);
    if ($viajeActivoId === 0 && !empty($misViajes)) {
        $_SESSION['viaje_activo_id'] = (int) $misViajes[0]['id'];
        $viajeActivoId = (int) $misViajes[0]['id'];
    }
}

// Carga los viajes del organizador y la lista de destinos disponibles.
$misViajes = obtenerViajesOrganizador($usuarioId);
$_destinos = getDestinos();
if ($viajeActivoId === 0 && !empty($misViajes)) {
    $_SESSION['viaje_activo_id'] = (int) $misViajes[0]['id'];
    $viajeActivoId = (int) $misViajes[0]['id'];
}
// Indica si las cards de servicios deben estar activas o bloqueadas.
$cardsActivas = $viajeActivoId > 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Metadatos y hojas de estilo del panel del organizador. -->
    <meta charset="UTF-8">
    <title>Gropper - Panel Organizador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="./views/estilo.css?v=20260407b">
</head>
<body class="bg-white">
<?php /* Menu compartido superior. */ ?>
<?php include_once "./views/menu.php"; ?>
<div class="container mt-5">
    <!-- Muestra mensajes de feedback tras crear, borrar o activar viajes. -->
    <?php if ($mensaje !== ""): ?><div class="alert alert-success"><?php echo $mensaje; ?></div><?php endif; ?>

    <!-- Bloque principal para crear viajes y elegir el viaje activo. -->
    <div class="card mb-4 shadow-sm overflow-hidden">
        <div class="card-header bg-primary text-white border-0 py-2">Crear viaje</div>
        <div class="card-body">
            <!-- Formulario de alta de un nuevo viaje. -->
            <form method="POST" class="row g-3">
                <input type="hidden" name="crear_viaje" value="1">
                <div class="col-md-6">
                    <select class="form-select" name="destino" required>
                        <option value="">Selecciona destino</option>
                        <?php foreach ($_destinos as $d): ?>
                            <option value="<?php echo htmlspecialchars($d); ?>"><?php echo htmlspecialchars($d); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2"><input class="form-control" type="number" step="0.01" min="1" name="presupuesto" placeholder="Presupuesto" required></div>
                <div class="col-md-4 d-flex gap-2">
                    <input class="form-control" type="date" name="fecha_inicio">
                    <input class="form-control" type="date" name="fecha_fin">
                </div>
                <div class="col-12"><button class="btn btn-success">Crear Viaje</button></div>
            </form>

            <!-- Selector del viaje activo y accion de borrado. -->
            <?php if (!empty($misViajes)): ?>
                <form method="GET" class="row g-2 mt-3">
                    <div class="col-md-8">
                        <select name="activar" class="form-select">
                            <?php foreach ($misViajes as $v): ?>
                                <option value="<?php echo (int)$v['id']; ?>" <?php echo ((int)$v['id'] === $viajeActivoId) ? 'selected' : ''; ?>>
                                    Viaje <?php echo (int)$v['id']; ?> - <?php echo htmlspecialchars($v['destino']); ?> (<?php echo number_format((float)$v['presupuesto_limite'], 2); ?> EUR)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex gap-2">
                        <button class="btn btn-outline-primary flex-fill">Seleccionar viaje activo</button>
                        <button type="button" class="btn btn-outline-danger" onclick="confirmarBorrar()" <?php echo empty($misViajes) ? 'disabled' : ''; ?>>
                            <i class="fas fa-trash"></i> Borrar
                        </button>
                    </div>
                </form>
                
                <!-- Formulario oculto para borrar -->
                <form id="formBorrar" method="POST" style="display: none;">
                    <input type="hidden" name="borrar_viaje_id" id="borrar_viaje_id">
                </form>
                
                <!-- Script que pide confirmacion antes de borrar un viaje. -->
                <script>
                function confirmarBorrar() {
                    var select = document.querySelector('select[name="activar"]');
                    var viajeId = select.value;
                    var viajeTexto = select.options[select.selectedIndex].text;
                    
                    if (confirm('¿Estás seguro de que quieres borrar este viaje?\n\n' + viajeTexto + '\n\nEsta acción no se puede deshacer.')) {
                        document.getElementById('borrar_viaje_id').value = viajeId;
                        document.getElementById('formBorrar').submit();
                    }
                }
                </script>
            <?php endif; ?>
        </div>
    </div>

    <!-- Aviso visual cuando todavia no hay un viaje activo con el que operar. -->
    <?php if (!$cardsActivas): ?>
        <div class="alert alert-info">Primero crea o selecciona un viaje para activar las cards de servicios.</div>
    <?php endif; ?>

    <!-- Cabecera de la zona de acceso a servicios. -->
    <div class="text-center mb-5">
        <h2 class="fw-bold section-title">
            <span class="title-main">Nuestros</span>
            <span class="title-accent">servicios</span>
        </h2>
        <div class="title-decoration"></div>
    </div>

    <!-- Tarjetas que llevan a alojamiento, vuelo y actividad. -->
    <div class="row g-4 mb-5 text-center">
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm p-4 bg-light">
                <div class="mb-3 text-primary"><i class="fas fa-bed fa-3x"></i></div>
                <h4 class="fw-bold service-title">
                    <span class="title-main">Aloja</span><span class="title-accent">mientos</span>
                </h4>
                <p class="text-muted small">Encuentra el lugar perfecto para todo el grupo.</p>
                <a href="<?php echo $cardsActivas ? 'servicios.php?viaje_id=' . $viajeActivoId . '&categoria=alojamiento' : '#'; ?>" class="btn btn-outline-primary rounded-pill mt-auto <?php echo !$cardsActivas ? 'disabled' : ''; ?>">Explorar</a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm p-4 bg-light">
                <div class="mb-3 text-primary"><i class="fas fa-plane fa-3x"></i></div>
                <h4 class="fw-bold service-title">
                    <span class="title-main">Vue</span><span class="title-accent">los</span>
                </h4>
                <p class="text-muted small">Gestiona billetes y horarios de forma conjunta.</p>
                <a href="<?php echo $cardsActivas ? 'servicios.php?viaje_id=' . $viajeActivoId . '&categoria=vuelo' : '#'; ?>" class="btn btn-outline-primary rounded-pill mt-auto <?php echo !$cardsActivas ? 'disabled' : ''; ?>">Explorar</a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm p-4 bg-light">
                <div class="mb-3 text-primary"><i class="fas fa-map-marked-alt fa-3x"></i></div>
                <h4 class="fw-bold service-title">
                    <span class="title-main">Activi</span><span class="title-accent">dades</span>
                </h4>
                <p class="text-muted small">Reserva tours y experiencias unicas.</p>
                <a href="<?php echo $cardsActivas ? 'servicios.php?viaje_id=' . $viajeActivoId . '&categoria=actividad' : '#'; ?>" class="btn btn-outline-primary rounded-pill mt-auto <?php echo !$cardsActivas ? 'disabled' : ''; ?>">Explorar</a>
            </div>
        </div>
    </div>

    <!-- Galeria final con ejemplos de destinos destacados. -->
    <div class="mt-5 pt-4 border-top">
        <h3 class="text-center fw-bold mb-4 section-title">
            <span class="title-main">Destinos</span>
            <span class="title-accent">destacados</span>
        </h3>
        <div class="title-decoration"></div>
        <div class="row row-cols-1 row-cols-md-5 g-4 text-center">
            <div class="col">
                <div class="card border-0">
                    <img src="./imagenes/NuevaYork.jpg" class="rounded-4 shadow-sm mb-2" style="height: 160px; object-fit: cover; object-position:center 35%;" alt="Nueva York">
                    <h6 class="fw-bold mb-0">Nueva York</h6>
                    <small class="text-muted">EE.UU</small>
                </div>
            </div>
            <div class="col">
                <div class="card border-0">
                    <img src="./imagenes/Tokio.jpg" class="rounded-4 shadow-sm mb-2" style="height: 160px; object-fit: cover;" alt="Tokio">
                    <h6 class="fw-bold mb-0">Tokio</h6>
                    <small class="text-muted">Japon</small>
                </div>
            </div>
            <div class="col">
                <div class="card border-0">
                    <img src="./imagenes/Bali.jpg" class="rounded-4 shadow-sm mb-2" style="height: 160px; object-fit: cover;" alt="Bali">
                    <h6 class="fw-bold mb-0">Bali</h6>
                    <small class="text-muted">Indonesia</small>
                </div>
            </div>
            <div class="col">
                <div class="card border-0">
                    <img src="./imagenes/Paris.jpeg" class="rounded-4 shadow-sm mb-2" style="height: 160px; object-fit: cover;" alt="Paris">
                    <h6 class="fw-bold mb-0">Paris</h6>
                    <small class="text-muted">Francia</small>
                </div>
            </div>
            <div class="col">
                <div class="card border-0">
                    <img src="./imagenes/Islandia.jpg" class="rounded-4 shadow-sm mb-2" style="height: 160px; object-fit: cover;" alt="Islandia">
                    <h6 class="fw-bold mb-0">Islandia</h6>
                    <small class="text-muted">Reikiavik</small>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
