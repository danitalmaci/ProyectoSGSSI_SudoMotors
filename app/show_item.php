<?php	
// ------------------------------------------------------------
// Ver información del vehículo	
// ------------------------------------------------------------
header("X-XSS-Protection: 1; mode=block");

session_start();
include 'connection.php';

$vehiculos_html = "";
$row = null;

if (isset($_GET['matricula'])) {
    $matricula = strtoupper(trim($_GET['matricula']));
    if (!preg_match('/^[0-9]{4}\s?[A-Z]{3}$/', $matricula)) {
        die("Matrícula no válida.");
    }

    $stmt = $conn->prepare("SELECT * FROM VEHICULO WHERE MATRICULA = ? LIMIT 1");
    $stmt->bind_param("s", $matricula);
    $stmt->execute();
    $result = $stmt->get_result();


    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $vehiculos_html .= '
        <table>
            <tr><th>Marca</th><td>' . htmlspecialchars($row["MARCA"]) . '</td></tr>
            <tr><th>Modelo</th><td>' . htmlspecialchars($row["MODELO"]) . '</td></tr>
            <tr><th>Matrícula</th><td>' . htmlspecialchars($row["MATRICULA"]) . '</td></tr>
            <tr><th>Año</th><td>' . htmlspecialchars($row["ANO"]) . '</td></tr>
            <tr><th>Kilómetros</th><td>' . htmlspecialchars($row["KMS"]) . '</td></tr>
        </table>
        ';
    } else {
        $vehiculos_html .= "<p>No se encontró información del vehículo.</p>";
    }
}

$successMessage = "";
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $successMessage = "Los datos del vehículo se han actualizado correctamente.";
}

$conn->close();

// Título de la página y head
$pageTitle = "Información del vehículo - SudoMotors";
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
  <h1>Datos del vehículo seleccionado</h1>
  <h3>Consulta o modifica la información registrada</h3>
</hgroup>

<?php if(!empty($successMessage)): ?>
  <article role="alert">
    <strong><?= htmlspecialchars($successMessage) ?></strong>
  </article>
<?php endif; ?>

<?= $vehiculos_html ?>

<?php if ($row): ?>
	
  <div style="display: flex; flex-direction: column; gap: 0.5rem; margin-top: 1rem;">
      <button type="button" id="modificar">Modificar datos</button>
      <button type="button" id="eliminar">Eliminar vehículo</button>
      <button type="button" id="cancelar">Cancelar</button>
  </div>
  <script src="js/botones.js"></script>
<?php endif; ?>

<?php include("includes/footer.php"); ?>
