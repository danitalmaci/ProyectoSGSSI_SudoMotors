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

function requireOwnerOrAdmin($owner) {
    requireLogin();
    if ($_SESSION['ROLE'] !== 'admin' && $_SESSION['USERNAME'] !== $owner) {
        http_response_code(403);
        die("Acceso denegado: no tienes permiso para acceder a este recurso.");
    }
}

