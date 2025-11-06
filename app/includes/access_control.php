<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


function requireLogin() {
    if (empty($_SESSION['USERNAME'])) {
        header("Location: login.php");
        exit;
    }
}

function requireAdmin() {
    if (!isset($_SESSION['ROLE']) || $_SESSION['ROLE'] !== 'admin') {
        $redirect = $_SERVER['HTTP_REFERER'] ?? 'items.php';

        // Si la URL ya tiene parámetros, usar "&", si no, usar "?"
        $separator = (strpos($redirect, '?') !== false) ? '&' : '?';
        header("Location: {$redirect}{$separator}error=admin");
        exit;
    }
}
