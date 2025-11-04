<?php
session_start();
header("X-XSS-Protection: 1; mode=block");

include 'connection.php'; // conexión a la BD

$message = "";
$tiempoRestante = 0;

// CONFIGURACIÓN
$max_intentos = 5;
$bloqueo_segundos = 60; // 1 minuto
$ip = $_SERVER['REMOTE_ADDR'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user = trim($_POST["user"]);
    $password = trim($_POST["contrasena"]);
    $v_user = $user;

    // Revisar intentos previos
    $stmt = $conn->prepare("SELECT INTENTOS, LAST_ATTEMPT FROM LOGIN_INTENTOS WHERE USERNAME = ? OR IP_ADDRESS = ?");
    $stmt->bind_param("ss", $user, $ip);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $intentos = $row['INTENTOS'];
        $last_attempt = strtotime($row['LAST_ATTEMPT']);
        $ahora = time();

        if ($intentos >= $max_intentos && ($ahora - $last_attempt) < $bloqueo_segundos) {
            $tiempoRestante = $bloqueo_segundos - ($ahora - $last_attempt);
            $message = "Demasiados intentos fallidos. Espera ";
        } else {
            if (($ahora - $last_attempt) >= $bloqueo_segundos) {
                $conn->query("DELETE FROM LOGIN_INTENTOS WHERE USERNAME = '$user' OR IP_ADDRESS = '$ip'");
            }
        }
    }

    // Intentar login
    if (empty($message)) {
        $stmt = $conn->prepare("SELECT * FROM USUARIO WHERE USERNAME = ? AND CONTRASENA = ?");
        $stmt->bind_param("ss", $user, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            // LOGIN CORRECTO
            $userData = $result->fetch_assoc();
            $_SESSION['USERNAME'] = $userData['USERNAME'];

            $conn->query("DELETE FROM LOGIN_INTENTOS WHERE USERNAME = '$user' OR IP_ADDRESS = '$ip'");
            registrarLog($conn, $user, $ip, 'EXITO', 'Inicio de sesión correcto');

            header("Location: items.php");
            exit;
        } else {
            // LOGIN FALLIDO
            registrarIntento($conn, $user, $ip);
            registrarLog($conn, $user, $ip, 'FALLO', 'Usuario o contraseña incorrectos');
            $message = "Usuario o contraseña incorrectos.";
        }
    }
}

// --- Funciones auxiliares ---
function registrarIntento($conn, $username, $ip) {
    $query = $conn->prepare("SELECT * FROM LOGIN_INTENTOS WHERE USERNAME = ? OR IP_ADDRESS = ?");
    $query->bind_param("ss", $username, $ip);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        $conn->query("UPDATE LOGIN_INTENTOS 
                      SET INTENTOS = INTENTOS + 1, LAST_ATTEMPT = NOW() 
                      WHERE USERNAME = '$username' OR IP_ADDRESS = '$ip'");
    } else {
        $conn->query("INSERT INTO LOGIN_INTENTOS (USERNAME, IP_ADDRESS, INTENTOS, LAST_ATTEMPT)
                      VALUES ('$username', '$ip', 1, NOW())");
    }
}

function registrarLog($conn, $username, $ip, $resultado, $detalles) {
    $navegador = $conn->real_escape_string($_SERVER['HTTP_USER_AGENT']);
    $username = $conn->real_escape_string($username);
    $ip = $conn->real_escape_string($ip);
    $detalles = $conn->real_escape_string($detalles);

    $conn->query("INSERT INTO LOGIN_LOGS (USERNAME, IP_ADDRESS, RESULTADO, NAVEGADOR, DETALLES)
                  VALUES ('$username', '$ip', '$resultado', '$navegador', '$detalles')");
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

$pageTitle = "Iniciar sesión - SudoMotors";
include("includes/head.php");
?>

<hgroup>
  <h1>Iniciar sesión</h1>
  <h3>Accede a tu cuenta de SudoMotors</h3>
</hgroup>

<?php if (!empty($message)): ?>
  <p style="color:red; font-weight:bold;">
    <?= htmlspecialchars($message) ?>
    <?php if ($tiempoRestante > 0): ?>
      <span id="contador"><?= $tiempoRestante ?></span> segundos.
    <?php endif; ?>
  </p>
<?php endif; ?>

<form id="login_form" method="POST" action="">
  <label for="user">Usuario:</label>
  <input type="text" id="user" name="user" required value="<?= htmlspecialchars($v_user) ?>" <?= $tiempoRestante > 0 ? 'disabled' : '' ?>>

  <label for="contrasena">Contraseña:</label>
  <input type="password" id="contrasena" name="contrasena" required <?= $tiempoRestante > 0 ? 'disabled' : '' ?>>
  <label><input type="checkbox" id="togglePass"> Mostrar contraseña</label>

  <button type="submit" <?= $tiempoRestante > 0 ? 'disabled' : '' ?>><?= $tiempoRestante > 0 ? 'Esperando...' : 'Iniciar sesión' ?></button>
  <span>¿No estás registrado? <a href="register.php">Regístrate</a></span>

  <button type="button" style="font-size: 16px; padding: 8px 10px;" onclick="window.location.href='index.php'">Cancelar</button>
</form>

<script>
  // Mostrar/ocultar contraseña
  const pass1 = document.getElementById('contrasena');
  const toggle1 = document.getElementById('togglePass');
  if (toggle1) {
    toggle1.addEventListener('change', () => {
      pass1.type = toggle1.checked ? 'text' : 'password';
    });
  }

  // Si hay cuenta atrás
  let contador = document.getElementById('contador');
  if (contador) {
    let tiempo = parseInt(contador.textContent);
    const boton = document.querySelector("button[type='submit']");

    const intervalo = setInterval(() => {
      tiempo--;
      contador.textContent = tiempo;

      if (tiempo <= 0) {
        clearInterval(intervalo);
        location.reload();
      }
    }, 1000);
  }
</script>

<?php include("includes/footer.php"); ?>

