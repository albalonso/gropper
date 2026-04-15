<?php
// Inicia o recupera la sesion para saber que tipo de usuario visita la portada.
session_start();
// Indica si existe una sesion iniciada.
$logueado = isset($_SESSION['usuario_id']);
// Calcula si el usuario autenticado es organizador.
$esOrganizador = $logueado && (($_SESSION['usuario_rol'] ?? '') === 'organizador');
// Recupera el viaje activo si ya existe en sesion.
$viajeActivoId = (int) ($_SESSION['viaje_activo_id'] ?? 0);

// Enlaces por defecto para usuarios anonimos: se les lleva al login.
$linkVuelo = "login.php";
$linkAlojamiento = "login.php";
$linkActividad = "login.php";

// Si hay organizador y viaje activo, los enlaces van directamente al buscador de servicios.
if ($logueado && $esOrganizador && $viajeActivoId > 0) {
    $linkVuelo = "servicios.php?viaje_id=" . $viajeActivoId . "&categoria=vuelo";
    $linkAlojamiento = "servicios.php?viaje_id=" . $viajeActivoId . "&categoria=alojamiento";
    $linkActividad = "servicios.php?viaje_id=" . $viajeActivoId . "&categoria=actividad";
// Si hay organizador pero aun no ha elegido viaje, se le manda a gestionarlo.
} elseif ($logueado && $esOrganizador) {
    $linkVuelo = "gestionar_viajes.php";
    $linkAlojamiento = "gestionar_viajes.php";
    $linkActividad = "gestionar_viajes.php";
// Si el usuario es acompanante, se le redirige a su dashboard.
} elseif ($logueado) {
    $linkVuelo = "dashboard.php";
    $linkAlojamiento = "dashboard.php";
    $linkActividad = "dashboard.php";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Metadatos y hojas de estilo de la pagina de inicio. -->
    <meta charset="UTF-8">
    <title>Gropper - Organiza tu viaje grupal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="./views/estilo.css?v=20260407b">
</head>
<body class="bg-white">
    <!-- Inserta el menu comun superior. -->
    <?php include_once "./views/menu.php"; ?>

    <!-- Hero principal con carrusel de destinos destacados. -->
    <header class="shadow-sm rounded-bottom-4 overflow-hidden">
        <div id="homeCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3500">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="./imagenes/Tokio.jpg" class="d-block w-100" style="height: 340px; object-fit: cover;" alt="Tokio">
                </div>
                <div class="carousel-item">
                    <img src="./imagenes/Bali.jpg" class="d-block w-100" style="height: 340px; object-fit: cover;" alt="Bali">
                </div>
                <div class="carousel-item">
                    <img src="./imagenes/Paris.jpeg" class="d-block w-100" style="height: 340px; object-fit: cover;" alt="Paris">
                </div>
                <div class="carousel-item">
                    <img src="./imagenes/NuevaYork.jpg" class="d-block w-100" style="height: 340px; object-fit: cover; object-position:center 35%;" alt="Nueva York">
                </div>
                <div class="carousel-item">
                    <img src="./imagenes/Islandia.jpg" class="d-block w-100" style="height: 340px; object-fit: cover;" alt="Islandia">
                </div>
            </div>
            <!-- Capa superpuesta con el mensaje principal de bienvenida. -->
            <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center text-center" style="background: rgba(20,32,64,.45);">
                <div class="text-white px-3">
                    <?php if (!$logueado): ?>
                        <!-- Mensaje comercial cuando todavia no hay sesion iniciada. -->
                        <h1 class="display-5 fw-bold main-title">
                            <span class="title-main">Organiza tu viaje grupal</span>
                            <span class="title-accent">soñado</span>
                        </h1>
                        <p class="lead opacity-75">La forma más fácil de viajar y compartir gastos</p>
                    <?php else: ?>
                <!-- Mensaje personalizado cuando el usuario ya esta dentro de la aplicacion. -->
                <h1 class="display-5 fw-bold">Te damos la bienvenida, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></h1>
                        <p class="lead opacity-75">Tu viaje esta listo para seguir planificandose</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <!-- Contenido principal de servicios y destinos. -->
    <main class="container mt-5">
        <!-- Encabezado de la seccion de servicios. -->
        <div class="text-center mb-5">
            <h2 class="fw-bold section-title">
                <span class="title-main">Nuestros</span>
                <span class="title-accent">servicios</span>
            </h2>
            <div class="title-decoration"></div>
        </div>

        <!-- Tarjetas de acceso rapido a las categorias clave del proyecto. -->
        <div class="row g-4 mb-5 text-center">
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm p-4 bg-light">
                    <div class="mb-3 text-primary"><i class="fas fa-bed fa-3x"></i></div>
                    <h4 class="fw-bold service-title">
                        <span class="title-main">Aloja</span><span class="title-accent">mientos</span>
                    </h4>
                    <p class="text-muted small">Encuentra el lugar perfecto para todo el grupo.</p>
                    <a href="<?php echo $linkAlojamiento; ?>" class="btn btn-outline-primary rounded-pill mt-auto">Explorar</a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm p-4 bg-light">
                    <div class="mb-3 text-primary"><i class="fas fa-plane fa-3x"></i></div>
                    <h4 class="fw-bold service-title">
                        <span class="title-main">Vue</span><span class="title-accent">los</span>
                    </h4>
                    <p class="text-muted small">Gestiona billetes y horarios de forma conjunta.</p>
                    <a href="<?php echo $linkVuelo; ?>" class="btn btn-outline-primary rounded-pill mt-auto">Explorar</a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm p-4 bg-light">
                    <div class="mb-3 text-primary"><i class="fas fa-map-marked-alt fa-3x"></i></div>
                    <h4 class="fw-bold service-title">
                        <span class="title-main">Activi</span><span class="title-accent">dades</span>
                    </h4>
                    <p class="text-muted small">Reserva tours y experiencias únicas.</p>
                    <a href="<?php echo $linkActividad; ?>" class="btn btn-outline-primary rounded-pill mt-auto">Explorar</a>
                </div>
            </div>
        </div>

        <!-- Bloque final con destinos visuales destacados. -->
        <div class="mt-5 pt-4 border-top">
            <h3 class="text-center fw-bold mb-4 section-title">
                <span class="title-main">Destinos</span>
                <span class="title-accent">destacados</span>
            </h3>
            <div class="title-decoration"></div>
            <div class="row row-cols-1 row-cols-md-5 g-4 text-center">
                <div class="col">
                    <div class="card border-0">
                        <img src="./imagenes/NuevaYork.jpg" class="rounded-4 shadow-sm mb-2" style="height: 160px; object-fit: cover; object-position: center 35%;">
                        <h6 class="fw-bold mb-0">Nueva York</h6>
                        <small class="text-muted">EE.UU</small>
                    </div>
                </div>
                <div class="col">
                    <div class="card border-0">
                        <img src="./imagenes/Tokio.jpg" class="rounded-4 shadow-sm mb-2" style="height: 160px; object-fit: cover;">
                        <h6 class="fw-bold mb-0">Tokio</h6>
                        <small class="text-muted">Japón</small>
                    </div>
                </div>
                <div class="col">
                    <div class="card border-0">
                        <img src="./imagenes/Bali.jpg" class="rounded-4 shadow-sm mb-2" style="height: 160px; object-fit: cover;">
                        <h6 class="fw-bold mb-0">Bali</h6>
                        <small class="text-muted">Indonesia</small>
                    </div>
                </div>
                <div class="col">
                    <div class="card border-0">
                        <img src="./imagenes/Paris.jpeg" class="rounded-4 shadow-sm mb-2" style="height: 160px; object-fit: cover;">
                        <h6 class="fw-bold mb-0">París</h6>
                        <small class="text-muted">Francia</small>
                    </div>
                </div>
                <div class="col">
                    <div class="card border-0">
                        <img src="./imagenes/Islandia.jpg" class="rounded-4 shadow-sm mb-2" style="height: 160px; object-fit: cover;">
                        <h6 class="fw-bold mb-0">Islandia</h6>
                        <small class="text-muted">Reikiavik</small>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Inserta el pie comun de la aplicacion. -->
    <?php include_once "./views/pie.php"; ?>
    <!-- Bootstrap JS para el carrusel y otros componentes. -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
