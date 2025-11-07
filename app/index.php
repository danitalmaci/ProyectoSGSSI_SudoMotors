<?php
// ------------------------------------------------------------
// PAGINA PRINCIPAL
// ------------------------------------------------------------

// Comienza la sesión configurando a su vez las cookies de sesión
session_start([
    'cookie_lifetime' => 0,           // La sesión se cierra cuando se cierra el navegador.
    'cookie_path' => '/',          
    'cookie_secure' => true,          // La cookie se envia sobre conexiones HTTPS.
    'cookie_httponly' => true,        // La cookie solo es accesible a través de HTTP.
    'cookie_samesite' => 'Lax',       // Define la política de SameSite.
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
  <?php if (isset($_SESSION['USERNAME'])): ?>
      <button type="button" id="perfil">Ver perfil</button>
      <button type="button" id="logout">Cerrar sesión</button>
      <button type="button" id="lista">Listado de vehículos</button>
  <?php else: ?>
      <button type="button" id="register">Registro de usuario</button>
      <button type="button" id="login">Iniciar sesión</button>
      <button type="button" id="lista">Listado de vehículos</button>
  <?php endif; ?>
</nav>
<script src="js/botones.js"></script>


<?php include("includes/footer.php"); ?>
