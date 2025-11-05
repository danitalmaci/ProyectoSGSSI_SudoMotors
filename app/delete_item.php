<?php
// ------------------------------------------------------------
// FORMULARIO ELIMINAR VEHÍCULO
// ------------------------------------------------------------
session_start();
header("X-XSS-Protection: 1; mode=block");
include 'connection.php';
include 'includes/security.php';
verificar_csrf();

// ------------------------------------------------------------
// CONTROL DE ACCESO
// ------------------------------------------------------------
require_once 'includes/init.php';
requireLogin();
requireAdmin();

if (!isset($_GET['matricula'])) {
    echo "No se ha especificado una matrícula.";
    exit;
}

// Validar y sanear matrícula
$matricula = strtoupper(trim($_GET['matricula'] ?? ''));
if (!preg_match('/^[0-9]{4}\s?[A-Z]{3}$/', $matricula)) {
    die("Matrícula no válida.");
}
$matricula = mysqli_real_escape_string($conn, $matricula);

$stmt = $conn->prepare("SELECT * FROM VEHICULO WHERE MATRICULA=?");
$stmt->bind_param("s", $matricula);
$stmt->execute();
$query = $stmt->get_result();
$stmt->close();

if (!$query || mysqli_num_rows($query) <= 0) {
    echo "Vehículo no encontrado.";
    exit;
}

$vehiculo_data = mysqli_fetch_assoc($query);

// Si se ha confirmado la eliminación (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare("DELETE FROM VEHICULO WHERE MATRICULA=? LIMIT 1");
    $stmt->bind_param("s", $matricula);
    
    if ($stmt->execute()) {
        header("Location: items.php?success=2");
        exit;
    } else {
        echo "Error al eliminar el vehículo: " . htmlspecialchars($stmt->error);
        exit;
    }
}

$conn->close();

// Título y head
$pageTitle = "Borrar vehículo - SudoMotors";
include("includes/head.php");
?>

<nav style="display:flex; justify-content:flex-end; gap:1rem; margin-bottom:1rem;">
  <a href="items.php">Mostrar vehículos</a>
  <?php if (isset($_SESSION['username'])): ?>
      <a href="show_user.php?user=<?= urlencode($_SESSION['username']) ?>">Ver perfil</a>
  <?php else: ?>
      <a href="login.php">Iniciar sesión</a>
  <?php endif; ?>
</nav>

<hgroup>
  <h1>Eliminar vehículo</h1>
  <h3>¿Seguro que deseas borrar este vehículo?</h3>
</hgroup>

<table>
  <tr><th>Matrícula</th><td><?= htmlspecialchars($vehiculo_data['MATRICULA']) ?></td></tr>
  <tr><th>Marca</th><td><?= htmlspecialchars($vehiculo_data['MARCA']) ?></td></tr>
  <tr><th>Modelo</th><td><?= htmlspecialchars($vehiculo_data['MODELO']) ?></td></tr>
</table>

<form method="post" 
      style="margin-top:1.5rem; display:flex; flex-direction:column; gap:0.5rem;">
  <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
  <button type="submit" class="contrast">Sí, borrar vehículo</button>
  <button type="button" onclick="window.location.href='show_item.php?matricula=<?= urlencode($matricula) ?>'">
      Cancelar
  </button>

</form>

<?php include("includes/footer.php"); ?>
