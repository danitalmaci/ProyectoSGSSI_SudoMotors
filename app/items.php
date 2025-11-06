<?php
session_start();

// Conexión a la base de datos
include 'connection.php';

// Consulta para obtener los vehículos
$sql = "SELECT MARCA, MODELO, MATRICULA FROM VEHICULO";
$result = $conn->query($sql);

// Guardar resultados en una variable para mostrarlos luego en el body
$vehiculos_html = "";

if ($result && $result->num_rows > 0) {
	// Se guarda el codigo HTML para la presntación de la tabla
    $vehiculos_html .= '
        <hgroup>
          <h1>Vehículos disponibles</h1>
          <h3>Selecciona una matrícula para ver detalles</h3>
        </hgroup>

        <table >
            <thead>
                <tr>
                    <th>Marca</th>
                    <th>Modelo</th>
                    <th>Matrícula</th>
                </tr>
            </thead>
            <tbody>
    ';
	
	// Se recorre la variable resultado donde se accede al listado de vehiculos, para ir mostrando la información de cada uno de ellos
    while ($row = $result->fetch_assoc()) {
        $vehiculos_html .= '
            <tr>
                <td>' . htmlspecialchars($row["MARCA"]) . '</td>
                <td>' . htmlspecialchars($row["MODELO"]) . '</td>
                <td>
                    <a href="show_item.php?matricula=' . urlencode($row["MATRICULA"]) . '">
                        ' . htmlspecialchars($row["MATRICULA"]) . '
                    </a>
                </td>
            </tr>
        ';
    }

    $vehiculos_html .= '
            </tbody>
        </table>
    ';
} else {
	// Si la variable resultado no estuviese vacia, se mostraria un mensaje indicando que no existen vehiculos guardados
    $vehiculos_html .= "<h3>No hay vehículos para mostrar actualmente.</h3>";
}

// Mensaje para indicar que el vehiculo ha sido añadido correctamente
$successMessage = "";
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $successMessage = "El vehículo se ha añadido correctamente.";
}
//Mensaje requiere privilegios
$errorMessage = "";
if (isset($_GET['error']) && $_GET['error'] === 'admin') {
    $errorMessage = "Acceso denegado: se requieren privilegios de administrador.";
}
// Mensaje para indicar que el vehiculo ha sido eliminado correctamente
if (isset($_GET['success']) && $_GET['success'] == 2) {
    $successMessage = "El vehículo se ha eliminado correctamente.";
}

$conn->close();

// Título de la página y head
$pageTitle = "Vehículos - SudoMotors";
include("includes/head.php");
?>
<?php if(!empty($errorMessage)): ?>
  <article role="alert" style="background-color:#ffe4e4; border:1px solid #cc0000; padding:0.5rem; margin-bottom:1rem;">
    <strong><?= htmlspecialchars($errorMessage) ?></strong>
  </article>
<?php endif; ?>

<?php if(!empty($successMessage)): ?>
  <article role="alert">
    <strong><?= htmlspecialchars($successMessage) ?></strong>
  </article>
<?php endif; ?>

<nav style="display:flex; justify-content:flex-end; gap:1rem; margin-bottom:1rem;">
  <?php if (isset($_SESSION['USERNAME'])): ?>
      <a href="show_user.php?user=<?= urlencode($_SESSION['USERNAME']) ?>">Ver perfil</a>
  <?php else: ?>
      <a href="login.php">Iniciar sesión</a>
  <?php endif; ?>
      <a href="index.php">Inicio</a>
</nav>

<?= $vehiculos_html ?>

<form action="add_item.php" method="GET" style="margin-top:1rem;">
  <button type="submit">Añadir vehículo</button>
</form>

<?php include("includes/footer.php"); ?>
