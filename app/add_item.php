<?php
// ------------------------------------------------------------
// FORMULARIO AÑADIR VEHÍCULO
// ------------------------------------------------------------
session_start();
header("X-XSS-Protection: 1; mode=block");

include 'connection.php';
include 'includes/access_control.php';
include 'includes/security.php';
verificar_csrf();


// ------------------------------------------------------------
// CONTROL DE ACCESO
// Solo los administradores pueden añadir vehículos
// ------------------------------------------------------------
requireLogin();
requireAdmin();

$message = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Limpiar y validar los datos recibidos
    $matricula  = strtoupper(trim($_POST['matricula'] ?? ''));
    $marca      = trim($_POST['marca'] ?? '');
    $modelo     = trim($_POST['modelo'] ?? '');
    $anio       = intval($_POST['ano'] ?? 0);
    $kilometros = intval($_POST['kms'] ?? 0);

    // Validaciones básicas
    if (!preg_match('/^[0-9]{4}\s?[A-Z]{3}$/', $matricula)) {
        $errors['matricula'] = "Formato de matrícula no válido (ejemplo: 1234 ABC).";
    }

    if ($anio < 1900 || $anio > intval(date("Y")) + 1) {
        $errors['ano'] = "Año no válido.";
    }

    if ($kilometros < 0) {
        $errors['kms'] = "Los kilómetros no pueden ser negativos.";
    }

    // Comprobar si ya existe la matrícula del vehículo
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT MATRICULA FROM VEHICULO WHERE MATRICULA = ?");
        $stmt->bind_param("s", $matricula);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errors['matricula'] = "Ya existe un vehículo con esta matrícula.";
        }
        $stmt->close();
    }

    // Insertar vehículo si no hay errores
    if (empty($errors)) {
        $insert = $conn->prepare(
            "INSERT INTO VEHICULO (MATRICULA, MARCA, MODELO, ANO, KMS) VALUES (?, ?, ?, ?, ?)"
        );
        $insert->bind_param("sssii", $matricula, $marca, $modelo, $anio, $kilometros);
        $result = $insert->execute();
        $insert->close();

        if ($result) {
            header("Location: items.php?success=1");
            exit;
        } else {
            $message = "Error al insertar vehículo: " . htmlspecialchars($conn->error, ENT_QUOTES, 'UTF-8');
        }
    }
}

$conn->close();

// Título y head
$pageTitle = "Añadir vehículo - SudoMotors";
include("includes/head.php");
?>

<nav style="display:flex; justify-content:flex-end; gap:1rem; margin-bottom:1rem;">
  <a href="items.php">Mostrar vehículos</a>
  <?php if (!empty($_SESSION['username'])): ?>
    <a href="show_user.php?user=<?= urlencode($_SESSION['username']) ?>">Ver perfil</a>
  <?php endif; ?>
</nav>

<hgroup>
  <h1>Añadir nuevo vehículo</h1>
  <h3>Rellena los datos y guarda</h3>
</hgroup>

<?php if (!empty($message)): ?>
  <article role="alert"><strong><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></strong></article>
<?php endif; ?>

<form id="item_add_form" method="POST" action="">
  <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
  <label>Matrícula
    <input type="text" name="matricula" required placeholder="1111 ZZZ">
    <?php if (isset($errors['matricula'])): ?>
      <span style="color:red; display:block; font-size:0.9em;"><?= htmlspecialchars($errors['matricula']); ?></span>
    <?php endif; ?>
  </label>

  <label>Marca
    <input type="text" name="marca">
    <?php if (isset($errors['marca'])): ?>
      <span style="color:red; display:block; font-size:0.9em;"><?= htmlspecialchars($errors['marca']); ?></span>
    <?php endif; ?>
  </label>

  <label>Modelo
    <input type="text" name="modelo">
    <?php if (isset($errors['modelo'])): ?>
      <span style="color:red; display:block; font-size:0.9em;"><?= htmlspecialchars($errors['modelo']); ?></span>
    <?php endif; ?>
  </label>

  <label>Año
    <input type="text" name="ano">
    <?php if (isset($errors['ano'])): ?>
      <span style="color:red; display:block; font-size:0.9em;"><?= htmlspecialchars($errors['ano']); ?></span>
    <?php endif; ?>
  </label>

  <label>Kilómetros
    <input type="text" name="kms">
    <?php if (isset($errors['kms'])): ?>
      <span style="color:red; display:block; font-size:0.9em;"><?= htmlspecialchars($errors['kms']); ?></span>
    <?php endif; ?>
  </label>

  <div style="display:flex; flex-direction:column; gap:0.5rem; margin-top:0.75rem;">
    <button type="button" id="item_add_submit">Guardar vehículo</button>
    <button type="button" onclick="window.location.href='items.php'">Cancelar</button>
  </div>
</form>

<script src="js/comprobacionVehiculo.js"></script>

<?php include("includes/footer.php"); ?>
