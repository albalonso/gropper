<!-- Navegacion principal reutilizada en las distintas pantallas del proyecto. -->
<nav class="navbar navbar-expand-lg navbar-light app-navbar py-3">
  <!-- Contenedor con ancho maximo para centrar el contenido del menu. -->
  <div class="container">
    <!-- Logo de la aplicacion y enlace rapido a la pagina principal. -->
    <a class="navbar-brand d-flex align-items-center fw-bold" href="index.php">
      <i class="fas fa-plane-departure me-2 text-primary"></i>
      <span class="brand-text">Gropper</span>
    </a>
    <!-- Zona de acciones del usuario situada a la derecha. -->
    <div class="ms-auto d-flex align-items-center gap-2">
      <?php if (!isset($_SESSION['usuario_id'])): ?>
        <!-- Acciones visibles cuando todavia no hay sesion iniciada. -->
        <a href="login.php" class="btn btn-link text-dark text-decoration-none app-link-btn">Iniciar sesion</a>
        <a href="SignUp.php" class="btn btn-primary rounded-pill px-4 shadow-sm">Registrarse</a>
      <?php else: ?>
        <!-- Acciones visibles para un usuario ya autenticado. -->
        <a href="dashboard.php" class="btn btn-outline-primary btn-sm rounded-pill">Dashboard</a>
        <a href="carrito.php" class="btn btn-outline-secondary btn-sm rounded-pill">Mi itinerario</a>
        <!-- Saludo personalizado con el nombre almacenado en la sesion. -->
        <span class="small text-muted app-user-chip">Hola, <strong><?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></strong></span>
        <!-- Boton para cerrar la sesion actual. -->
        <a href="logout.php" class="btn btn-outline-danger btn-sm rounded-pill">Salir</a>
      <?php endif; ?>
    </div>
  </div>
</nav>
