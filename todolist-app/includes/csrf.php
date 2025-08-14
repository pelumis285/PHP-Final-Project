<?php
// csrf.php - CSRF token helpers
// This file contains functions for handling session management and CSRF tokens securely.

function safe_session_start() {
    if (session_status() === PHP_SESSION_NONE) {
        // set secure session cookie parameters
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'secure' => false, // set to true when using HTTPS
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
        session_start();
    }
}

function ensure_csrf_token() {
    safe_session_start();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function get_csrf_input_field() {
    ensure_csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($_SESSION['csrf_token']) . '">';
}

function verify_csrf_token($token) {
    safe_session_start();
    if (empty($_SESSION['csrf_token']) || empty($token)) return false;
    return hash_equals($_SESSION['csrf_token'], $token);
}
?>