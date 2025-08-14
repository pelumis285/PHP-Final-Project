<?php
// delete_user.php - Deletes a user (requires login).
require_once './includes/db_config.php';
require_once './includes/csrf.php';
safe_session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        die("Invalid request (CSRF).");
    }
    $userId = (int)$_POST['user_id'];
    if ($userId > 0) {
        $stmt = $conn->prepare("SELECT image_path FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->bind_result($image_path);
        $stmt->fetch();
        $stmt->close();
        if ($image_path && file_exists($image_path)) {
            @unlink($image_path);
        }
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: users.php");
    exit();
}
header("Location: users.php");
exit();
?>