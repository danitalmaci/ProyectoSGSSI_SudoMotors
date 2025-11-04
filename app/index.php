<?php
// Comienza la sesión configurando a su vez las cookies de sesión
session_start([
    'cookie_lifetime' => 0,           // La sesión se cierra cuando se cierra el navegador.
    'cookie_path' => '/',          
    //'cookie_secure' => true,          // La cookie se envia sobre conexiones HTTPS.
    //'cookie_httponly' => true,        // La cookie solo es accesible a través de HTTP.
    //'cookie_samesite' => 'Lax',       // Define la política de SameSite.
]);


$pageTitle = "Inicio - SudoMotors";
include("includes/head.php");
?>

<hgroup>
  <h1>Home</h1>
  <h3>Bienvenido a SudoMotors</h3>
</hgroup>

<!-- Contenedor vertical -->
<nav style="display: flex; flex-direction: column; gap: 1rem;">
  <?php if (isset($_SESSION['username'])): ?>
      <a href="show_user.php?user=<?= urlencode($_SESSION['username']) ?>" role="button" class="contrast">Ver perfil</a>
      <a href="login.php?logout=1" role="button" class="contrast">Cerrar sesión</a>
  <?php else: ?>
      <a href="register.php" role="button" class="contrast">Registro de usuario</a>
      <a href="login.php" role="button" class="contrast">Iniciar sesión</a>
  <?php endif; ?>
  <a href="items.php" role="button">Listado de vehículos</a>
</nav>


<?php include("includes/footer.php"); ?>
