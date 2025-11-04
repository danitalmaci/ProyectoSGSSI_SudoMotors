<?php
// ------------------------------------------------------------
// Formulario para registrarse
// ------------------------------------------------------------
header("X-XSS-Protection: 1; mode=block");

include 'connection.php'; 
include 'includes/security.php';
verificar_csrf();
session_start();

$message = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username     = htmlspecialchars(trim($_POST['usuario'] ?? ''), ENT_QUOTES, 'UTF-8');
  $password     = $_POST['contrasena'] ?? '';
  $confirm_password = $_POST['confirmar_contrasena'] ?? '';
  $nombre       = htmlspecialchars(trim($_POST['nombre'] ?? ''), ENT_QUOTES, 'UTF-8');
  $apellidos    = htmlspecialchars(trim($_POST['apellidos'] ?? ''), ENT_QUOTES, 'UTF-8');
  $dni          = strtoupper(trim($_POST['dni'] ?? ''));
  $email        = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
  $telefono     = filter_var($_POST['telefono'] ?? '', FILTER_SANITIZE_NUMBER_INT);
  $f_nacimiento = $_POST['f_nacimiento'] ?? '';


  // Comprobar contraseñas
  if ($password !== $confirm_password) {
      $errors['confirmar_contrasena'] = "Las contraseñas no coinciden.";
  }

  // Comprobar datos repetidos
  $stmt_check = $conn->prepare("SELECT USERNAME, EMAIL, DNI FROM USUARIO WHERE USERNAME=? OR EMAIL=? OR DNI=? LIMIT 1");
  $stmt_check->bind_param("sss", $username, $email, $dni);
  $stmt_check->execute();
  $res = $stmt_check->get_result();
  $exists = $res->fetch_assoc();

  if ($exists) {
    if ($exists['USERNAME'] === $username) $errors['usuario'] = "El usuario ya existe.";
    if ($exists['DNI'] === $dni) $errors['dni'] = "El DNI ya está registrado.";
    if ($exists['EMAIL'] === $email) $errors['email'] = "El email ya está en uso.";
  }

  // Insertar usuario si no hay errores
  if (empty($errors)) {
    $stmt_insert = $conn->prepare("INSERT INTO USUARIO (DNI, NOMBRE, APELLIDOS, TELEFONO, EMAIL, F_NACIMIENTO, CONTRASENA, USERNAME) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt_insert->bind_param("ssssssss", $dni, $nombre, $apellidos, $telefono, $email, $f_nacimiento, $hashed_password, $username);


    if ($stmt_insert->execute()) {
      $_SESSION['username'] = $username;
      header("Location: items.php");
      exit;
    } else {
      $message = "Error al insertar usuario: " . mysqli_error($conn);
    }
  }
}

// Recuperamos los valores para que el formulario no se borre
$v_usuario   = $_POST['usuario'] ?? '';
$v_nombre    = $_POST['nombre'] ?? '';
$v_apellidos = $_POST['apellidos'] ?? '';
$v_dni       = $_POST['dni'] ?? '';
$v_email     = $_POST['email'] ?? '';
$v_telefono  = $_POST['telefono'] ?? '';
$v_f_nac     = $_POST['f_nacimiento'] ?? '';

$conn->close();

// Título de la página
$pageTitle = "Registro - SudoMotors";
include("includes/head.php");
?>

<hgroup>
  <h1>Registro de usuario</h1>
  <h3>Crea tu cuenta para acceder a SudoMotors</h3>
</hgroup>

<?php if (!empty($message)): ?>
  <p style="color:red;"><?php echo $message; ?></p>
<?php endif; ?>

<form id="register_form" method="POST" action="">
  <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
  <label>Usuario:
    <input type="text" name="usuario" required value="<?php echo $v_usuario; ?>">
    <?php if (isset($errors['usuario'])): ?>
      <span style="color:red;"><?php echo $errors['usuario']; ?></span>
    <?php endif; ?>
  </label>

  <label>Contraseña:</label>
  <div style="display:flex; align-items:center; gap:10px;">
    <input type="password" name="contrasena" id="contrasena" required>
    <label><input type="checkbox" id="togglePass"> Mostrar</label>
  </div>

  <label>Confirmar contraseña:
    <input type="password" name="confirmar_contrasena" id="confirmar_contrasena" required>
    <?php if (isset($errors['confirmar_contrasena'])): ?>
      <span style="color:red;"><?php echo $errors['confirmar_contrasena']; ?></span>
    <?php endif; ?>
  </label>

  <label>Nombre:
    <input type="text" name="nombre" required value="<?php echo $v_nombre; ?>">
  </label>

  <label>Apellidos:
    <input type="text" name="apellidos" required value="<?php echo $v_apellidos; ?>">
  </label>

  <label>DNI:
    <input type="text" name="dni" required placeholder="11111111-Z" value="<?php echo $v_dni; ?>">
    <?php if (isset($errors['dni'])): ?>
      <span style="color:red;"><?php echo $errors['dni']; ?></span>
    <?php endif; ?>
  </label>

  <label>Email:
    <input type="email" name="email" required value="<?php echo $v_email; ?>">
    <?php if (isset($errors['email'])): ?>
      <span style="color:red;"><?php echo $errors['email']; ?></span>
    <?php endif; ?>
  </label>

  <label>Teléfono:
    <input type="text" name="telefono" required placeholder="9 dígitos" value="<?php echo $v_telefono; ?>">
  </label>

  <label>Fecha de nacimiento:
    <input type="date" name="f_nacimiento" required value="<?php echo $v_f_nac; ?>">
  </label>

  <div style="display: flex; flex-direction: column; gap: 0.5rem; margin-top: 1rem;">
    <button type="button" id="register_submit">Registrarme</button>
    <button type="button" onclick="window.location.href='index.php'">Cancelar</button>
  </div>
</form>

<script src="js/comprobacionDatos.js"></script>
<script>
  const pass1 = document.getElementById('contrasena');
  const pass2 = document.getElementById('confirmar_contrasena');
  const toggle1 = document.getElementById('togglePass');

  toggle1.addEventListener('change', () => {
    pass1.type = toggle1.checked ? 'text' : 'password';
    pass2.type = toggle1.checked ? 'text' : 'password';
  });
</script>

<?php include("includes/footer.php"); ?>
