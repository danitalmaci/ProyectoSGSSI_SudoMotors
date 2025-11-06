<?php
session_start();

function requireLogin() {
    if (empty($_SESSION['USERNAME'])) {
        header("Location: login.php");
        exit;
    }
}

function requireAdmin() {
    requireLogin();
    if ($_SESSION['ROLE'] !== 'admin') {
        http_response_code(403);
        die("Acceso denegado: se requieren privilegios de administrador.");
    }
}
?>
