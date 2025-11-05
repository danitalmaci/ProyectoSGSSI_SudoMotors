<?php
session_start();
// ------------------------------------------------------------
// Ver Perfil
// ------------------------------------------------------------
header("X-XSS-Protection: 1; mode=block");

include 'connection.php';
include 'includes/access_control.php';

// Exigir sesión iniciada
requireLogin();

$username = $_SESSION['USERNAME'];

// Cargar datos del usuario
$stmt = $conn->prepare("SELECT * FROM USUARIO WHERE USERNAME = ? LIMIT 1");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("Error en la consulta: " . $conn->error);
}

if ($result->num_rows > 0) {
    $userRow = $result->fetch_assoc();
    $fullname = $userRow['NOMBRE'] . ' ' . $userRow['APELLIDOS'];
} else {
    die("Usuario no encontrado.");
}

$successMessage = "";
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $successMessage = "Tus datos se han actualizado correctamente";
}

$conn->close();

// Título y head
$pageTitle = "Perfil de " . htmlspecialchars($fullname) . " - SudoMotors";
include("includes/head.php");
?>

<nav style="display:flex; justify-content:flex-end; gap:1rem; margin-bottom:1rem;">
  <a href="items.php">Mostrar vehículos</a>
  <a href="index.php">Inicio</a>
</nav>

<hgroup>
  <h1>Perfil de <?= htmlspecialchars($fullname) ?></h1>
  <h3>Información de tu cuenta</h3>
</hgroup>

<?php if($successMessage): ?>
  <article role="alert">
    <strong><?= htmlspecialchars($successMessage) ?></strong>
  </article>
<?php endif; ?>

<p><strong>Usuario:</strong> <?= htmlspecialchars($userRow['USERNAME']) ?></p>
<p><strong>Email:</strong> <?= htmlspecialchars($userRow['EMAIL']) ?></p>
<p><strong>Teléfono:</strong> <?= htmlspecialchars($userRow['TELEFONO']) ?></p>
<p><strong>DNI:</strong> <?= htmlspecialchars($userRow['DNI']) ?></p>

<div style="display: flex; flex-direction: column; gap: 0.5rem; margin-top: 1rem;">
      <button type="button" id="modificar">Modificar datos</button>
      <button type="button" id="logout">Cerrar sesión</button>
      <button type="button" id="cancelar">Cancelar</button>
</div>
<script src="js/botones.js"></script>
<?php include("includes/footer.php"); ?>
