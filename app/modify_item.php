<?php
// ------------------------------------------------------------
// FORMULARIO MODIFICAR VEHÍCULO
// ------------------------------------------------------------
session_start();
header("X-XSS-Protection: 1; mode=block");
include 'connection.php';

// ------------------------------------------------------------
// CONTROL DE ACCESO
// ------------------------------------------------------------
requireLogin();
requireAdmin();

// Comprobar si la URL contiene el parámetro necesario, la matrícula
if (!isset($_GET['matricula'])) {
    // Si no, redirije a items.php
    header("Location: items.php");
    exit;
}

$matricula = strtoupper(trim($_GET['matricula']));
if (!preg_match('/^[0-9]{4}\s?[A-Z]{3}$/', $matricula)) {
    die("Matrícula no válida.");
}

// Inicializar variables
$errors = [];

// Buscar los datos del vehículo a partir del parámetro del formulario
$stmt = $conn->prepare("SELECT * FROM VEHICULO WHERE MATRICULA=? LIMIT 1");
$stmt->bind_param("s", $matricula);
$stmt->execute();
$query = $stmt->get_result();

// Si no hay resultados, se guarda null
if (!$query || mysqli_num_rows($query) == 0) {
    $vehiculo_data = null;
} 
// Si hay resultados, se guardan los datos actuales del vehículo
else {
    $vehiculo_data = mysqli_fetch_assoc($query);
}

// Actualizar los datos del vehículo
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger los datos nuevos del vehículo a partir del formulario
    $new_matricula = strtoupper(trim($_POST['matricula'] ?? '')); 
    $new_marca = htmlspecialchars(trim($_POST['marca'] ?? ''), ENT_QUOTES, 'UTF-8'); 
    $new_modelo = htmlspecialchars(trim($_POST['modelo'] ?? ''), ENT_QUOTES, 'UTF-8'); 
    $new_ano = filter_var($_POST['ano'] ?? '', FILTER_VALIDATE_INT); 
    $new_kms = filter_var($_POST['kms'] ?? '', FILTER_VALIDATE_INT);

    // Comprobar si ya existe otro coche con la misma matrícula
    $stmt = $conn->prepare("SELECT * FROM VEHICULO WHERE MATRICULA=? AND MATRICULA<>?");
    $stmt->bind_param("ss", $new_matricula, $matricula);
    $stmt->execute();
    $check_vehiculo = $stmt->get_result();
    $stmt->close();
    
    // Si ya existe un coche con la matrícula, se almacena el error
    if ($check_vehiculo && mysqli_num_rows($check_vehiculo) > 0) {
        $errors['matricula'] = "La matrícula ya existe.";
    }

    // Si no hay errores se sigue con la modificacion de datos
    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE VEHICULO SET MATRICULA=?, MARCA=?, MODELO=?, ANO=?, KMS=? WHERE MATRICULA=?");
	$stmt->bind_param("ssssss", $new_matricula, $new_marca, $new_modelo, $new_ano, $new_kms, $matricula);
	$result = $stmt->execute();
	$stmt->close();


        // Si la operación ha sido correcta, redirije a la página show_item con el flag success, que indica el éxito de la ejecución
        if ($result) {
            header("Location: show_item.php?matricula=" . urlencode($new_matricula) . "&success=1");
            exit;
        } 

        // Si la operación no se ha ejecutado correctamente, se almacena el error
        else {
		error_log("Error al actualizar vehículo: " . $conn->error);
		$errors['general'] = "Error al actualizar el vehículo. Inténtelo más tarde.";
        }
    } 

    // En caso de errores, se evita el borrado de datos introducidos en el formulario
    else {
        $vehiculo_data = [
            'MATRICULA' => $new_matricula,
            'MARCA'     => $new_marca,
            'MODELO'    => $new_modelo,
            'ANO'       => $new_ano,
            'KMS'       => $new_kms,
        ];
    }
}

// Se cierra la conexión con la base de datos
$conn->close();

// Título de la página
$pageTitle = "Modificar vehículo - SudoMotors";
include("includes/head.php");
?>


<nav style="display:flex; justify-content:flex-end; gap:1rem; margin-bottom:1rem;">
    <a href="items.php">Mostrar vehículos</a>

    <?php if (!empty($_SESSION['username'])): ?>
        <a href="show_user.php?user=<?= urlencode($_SESSION['username']) ?>">Ver perfil</a>
    <?php endif; ?>
</nav>


<hgroup>
    <h1>Modificar datos del vehículo</h1>
    <h3>Actualiza la información y guarda los cambios</h3>
</hgroup>

<?php if (!empty($errors['general'])): ?>
    <article role="alert"><strong><?= htmlspecialchars($errors['general']) ?></strong></article>
<?php endif; ?>

<?php if ($notFound): ?>
    <article role="alert"><strong>Vehículo no encontrado.</strong></article>
    <button type="button" onclick="window.location.href='items.php'">Volver</button>
    <?php include("includes/footer.php"); exit; ?>
<?php endif; ?>

<form id="item_modify_form" method="post" action="">
    <label>Matrícula
        <input
            type="text"
            name="matricula"
            required
            placeholder="1111 ZZZ"
            value="<?= htmlspecialchars($vehiculo_data['MATRICULA']) ?>"
        >
        <?php if (isset($errors['matricula'])): ?>
            <span style="color:red; display:block; font-size:0.9em;"><?= htmlspecialchars($errors['matricula']) ?></span>
        <?php endif; ?>
    </label>

    <label>Marca
        <input type="text" name="marca" required value="<?= htmlspecialchars($vehiculo_data['MARCA']) ?>">
    </label>

    <label>Modelo
        <input type="text" name="modelo" required value="<?= htmlspecialchars($vehiculo_data['MODELO']) ?>">
    </label>

    <label>Año
        <input type="text" name="ano" required value="<?= htmlspecialchars($vehiculo_data['ANO']) ?>">
    </label>

    <label>Kilómetros
        <input type="text" name="kms" required value="<?= htmlspecialchars($vehiculo_data['KMS']) ?>">
    </label>

    <div style="display:flex; flex-direction:column; gap:0.5rem; margin-top:0.75rem;">
        <button type="button" id="item_modify_submit">Guardar cambios</button>
        <button type="button" onclick="window.location.href='show_item.php?matricula=<?= urlencode($vehiculo_data['MATRICULA']) ?>'">
            Cancelar
        </button>
    </div>
</form>

<script src="js/comprobacionVehiculo.js"></script>

<?php include("includes/footer.php"); ?>
