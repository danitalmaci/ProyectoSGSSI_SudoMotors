<?php
// CONFIGURACIÓN DE SEGURIDAD GLOBAL//

// Cabeceras de seguridad HTTP
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("Content-Security-Policy: default-src 'self'; style-src 'self' 'unsafe-inline'; img-src 'self' data:; script-src 'self'; object-src 'none';");

// Solo configuramos las cookies si la sesión NO está activa
if (session_status() === PHP_SESSION_NONE) {
  session_set_cookie_params([
    'httponly' => true,
    'secure' => true,  
    'samesite' => 'Strict'
  ]);
  session_start();
}

// Token CSRF (si no existe, se crea)
if (!isset($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

?>

<!-- includes/head.php -->
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $pageTitle ?? 'SudoMotors'; ?></title>

  <!-- PicoCSS principal -->
  <link rel="stylesheet" href="css/estilo_principal.css">

  <!-- Estilo para centrar todo -->
  <style>
    body {
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      text-align: center;
    }
    main {
      width: 100%;
      max-width: 500px;
    }
    footer {
      margin-top: 2rem;
      font-size: 0.9rem;
      color: var(--pico-muted-color);
    }
  </style>
  <link rel="icon" href="/media/favicon.png" type="image/png">
</head>
<body>
  <main>