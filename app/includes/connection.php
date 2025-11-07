<?php
// ------------------------------------------------------------
// CONEXIÓN
// ------------------------------------------------------------

$hostname = "db";
$bdusername = "admin";
$password = "Sudo@Motors02";
$db = "database";

// Crear la conexión con la base de datos
$conn = mysqli_connect($hostname, $bdusername, $password, $db);

// Verificar la conexión
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

?>
