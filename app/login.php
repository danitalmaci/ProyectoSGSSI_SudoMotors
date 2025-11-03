<?php
// Comienza la sesión
session_start();

header("X-XSS-Protection: 1; mode=block");

include 'connection.php'; // Conexión a la BD

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user = $_POST["user"];
    $password = $_POST["contrasena"];
    $v_user = $user;

    $stmt = $conn->prepare("SELECT * FROM USUARIO WHERE USERNAME = ? AND CONTRASENA = ?");
    $stmt->bind_param("ss", $user, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $userData = $result->fetch_assoc();
        $_SESSION['username'] = $userData['USERNAME'];
        header("Location: items.php");
        exit;
    } else {
        $message = "Usuario o contraseña incorrecto.";
    }
}

// Cerrar sesión si se llama con ?logout=1
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
}

$v_user = $v_user ?? '';

$conn->close();

// Título de la página
$pageTitle = "Iniciar sesión - SudoMotors";
include("includes/head.php");
?>

<hgroup>
  <h1>Iniciar sesión</h1>
  <h3>Accede a tu cuenta de SudoMotors</h3>
</hgroup>

<?php if (!empty($message)): ?>
  <p style="color:red;"><?= $message ?></p>
<?php endif; ?>

<form id="login_form" method="POST" action="">
  <label for="user">Usuario:</label>
  <input type="text" id="user" name="user" required value="<?= htmlspecialchars($v_user) ?>">

  <label for="contrasena">Contraseña:</label>
  <input type="password" id="contrasena" name="contrasena" required>
  <label><input type="checkbox" id="togglePass"> Mostrar contraseña</label>

  <button type="submit">Iniciar sesión</button>
  <span>¿No estás registrado? <a href="register.php">Regístrate</a></span>

  <button type="button" style="font-size: 16px; padding: 8px 10px;" onclick="window.location.href='index.php'">Cancelar</button>
</form>

<script>
  const pass1 = document.getElementById('contrasena');
  const toggle1 = document.getElementById('togglePass');
  toggle1.addEventListener('change', () => {
    pass1.type = toggle1.checked ? 'text' : 'password';
  });
</script>

<?php include("includes/footer.php"); ?>
