<?php session_start();
// ------------------------------------------------------------
// Formulario para modificar Usuario
// ------------------------------------------------------------
header("X-XSS-Protection: 1; mode=block");

// Datos de conexión a la base de datos
include 'connection.php';

// Comprobar si el usuario está identificado
if (!isset($_SESSION['username'])) {
	// Si no está identificado le lleva a la página de iniciar sesión
   	header("Location: login.php");
    exit;
}

// Buscar los datos del usuario
$stmt = $conn->prepare("SELECT * FROM USUARIO WHERE USERNAME=? LIMIT 1");
$stmt->bind_param("s", $_SESSION['username']);
$stmt->execute();
$query = $stmt->get_result();


// En caso de no encontrar al usuario, lo notifica
if (!$query || mysqli_num_rows($query) === 0) {
    echo "Usuario no encontrado.";
    exit;
}

// Se guardan los datos actuales del vehículo
$user_data = mysqli_fetch_assoc($query);

// Actualizar los datos del usuairo
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger los datos nuevos del usuario a partir del formulario
    $new_dni          = strtoupper(trim($_POST['dni'] ?? ''));
    $new_nombre       = htmlspecialchars(trim($_POST['nombre'] ?? ''), ENT_QUOTES, 'UTF-8');
    $new_apellidos    = htmlspecialchars(trim($_POST['apellidos'] ?? ''), ENT_QUOTES, 'UTF-8');
    $new_telefono     = filter_var($_POST['telefono'] ?? '', FILTER_SANITIZE_NUMBER_INT);
    $new_email        = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $new_f_nacimiento = $_POST['f_nacimiento'] ?? '';
    $new_username     = htmlspecialchars(trim($_POST['username'] ?? ''), ENT_QUOTES, 'UTF-8');
    $new_contrasena   = $_POST['contrasena'] ?? '';
    $confirm_contrasena = $_POST['confirmar_contrasena'] ?? '';

    	
    // Comprobar coincidencia de contraseñas
  	if ($new_contrasena !== $confirm_contrasena) {
    		$errors['confirmar_contrasena'] = "Las contraseñas no coinciden.";
	}

	// Comprobar datos repetidos
  	$stmt_check = $conn->prepare("SELECT USERNAME, DNI FROM USUARIO WHERE (USERNAME=? OR DNI=?) AND USERNAME<>?");
	$stmt_check->bind_param("sss", $new_username, $new_dni, $_SESSION['username']);
	$stmt_check->execute();
	$res = $stmt_check->get_result();
  	$exists = mysqli_fetch_assoc($res);

  	if ($exists) {
  		if ($exists['USERNAME'] === $new_username) $errors['usuario'] = "El usuario ya existe.";
  		if ($exists['DNI'] === $new_dni) $errors['dni'] = "El DNI ya está registrado.";
 	} 
 	
 	if(empty($errors)){// Si no hay errores, entonces se actualizan los datos
   		$stmt_update = $conn->prepare("UPDATE USUARIO SET NOMBRE=?, APELLIDOS=?, TELEFONO=?, EMAIL=?, 
   				F_NACIMIENTO=?, CONTRASENA=?, USERNAME=?, DNI=? WHERE USERNAME=? LIMIT 1");
		$stmt_update->bind_param("sssssssss",
    					$new_nombre,
    					$new_apellidos,
    					$new_telefono,
    					$new_email,
    					$new_f_nacimiento,
    					$new_contrasena,
    					$new_username,
   					$new_dni,
   					$_SESSION['username']);

		
		// Se actualiza el username en la variable de sesión y se redirije a la página para visualizar los datos del usuario
    		if ($stmt_update->execute()) {
    			$_SESSION['username'] = $new_username;
    			header("Location: show_user.php?user=" . urlencode($new_username));
    			exit;
		}

    	}
}

// Cerrar conexión con la base de datos
$conn->close();

// Título de la página
$pageTitle = "Registro - SudoMotors";
include("includes/head.php");
?>


<!DOCTYPE html>
<html>
<head>
    <title>Modificar usuario</title>
</head>
	
<body>
	<div style="position: absolute; top: 20px; right: 20px;">
    	<a href="items.php">Inicio </a><br>
	</div>

	
	<h1>Modificar tus datos</h1>

	
	<form id="user_modify_form" method="post">
		<label>Username:</label>
    	<input type="text" name="username" value="<?= htmlspecialchars($user_data['USERNAME']) ?>" required><br>
    	<?php if (isset($errors['dni'])): ?>
      	<span style="color:red;"><?php echo $errors['usuario']; ?></span>
    	<?php endif; ?>
    	
    	<label>Contraseña:</label>
		<div style="display:flex; align-items:center; gap:10px;">
    		<input type="password" name="contrasena" id="contrasena" value="<?= htmlspecialchars($user_data['CONTRASENA']) ?>" required>
    		<label><input type="checkbox" id="togglePass"> Mostrar</label>
		</div><br>

		<label>Confirmar contraseña:</label>
		<input type="password" name="confirmar_contrasena" id="confirmar_contrasena" value="<?= htmlspecialchars($user_data['CONTRASENA']) ?>" required><br>

    	<label>Nombre:</label>
    	<input type="text" name="nombre" value="<?= htmlspecialchars($user_data['NOMBRE']) ?>" required><br>

    	<label>Apellidos:</label>
    	<input type="text" name="apellidos" value="<?= htmlspecialchars($user_data['APELLIDOS']) ?>" required><br>
    	
    	<label>DNI:</label>
    	<input type="text" name="dni" value="<?= htmlspecialchars($user_data['DNI']) ?>" required><br>
    	<?php if (isset($errors['dni'])): ?>
     	<span style="color:red;"><?php echo $errors['dni']; ?></span>
  		<?php endif; ?>
    	
    	<label>Email:</label>
    	<input type="email" name="email" value="<?= htmlspecialchars($user_data['EMAIL']) ?>" required><br>

    	<label>Teléfono:</label>
    	<input type="text" name="telefono" value="<?= htmlspecialchars($user_data['TELEFONO']) ?>" required><br>

		<label>Fecha de nacimiento:</label>
		<input type="date" name="f_nacimiento" value="<?= htmlspecialchars($user_data['F_NACIMIENTO']) ?>" required><br>

		<button type="button" id="user_modify_submit">Guardar cambios</button>
		<button type="button" onclick="window.location.href='show_user.php?user=<?= urlencode($_SESSION['username']) ?>'">
    		Cancelar
		</button>
	</form>

	
	<script src="js/comprobacionDatos.js"></script>

	
	<script>
  		const pass1 = document.getElementById('contrasena');
  		const pass2 = document.getElementById('confirmar_contrasena');
  		const toggle = document.getElementById('togglePass');

  		toggle.addEventListener('change', () => {
    		const type = toggle.checked ? 'text' : 'password';
    		pass1.type = type;
    		pass2.type = type;
  		});
	</script>

</body>

</html>

<?php include("includes/footer.php"); ?>

