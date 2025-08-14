<?php
// change_password.php - Allow logged-in users to change their password.
require_once './includes/db_config.php';
require_once './includes/csrf.php';
safe_session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        $errors[] = "Invalid request (CSRF token mismatch). Please try again.";
    } else {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_new_password = $_POST['confirm_new_password'];
        $userId = $_SESSION['user_id'];

        if (empty($current_password) || empty($new_password) || empty($confirm_new_password)) {
            $errors[] = "All fields are required.";
        }
        if ($new_password !== $confirm_new_password) {
            $errors[] = "New passwords do not match.";
        }
        if (strlen($new_password) < 8) {
            $errors[] = "New password must be at least 8 characters long.";
        }
        if (empty($errors)) {
            $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $stmt->bind_result($hashed_password);
            $stmt->fetch();
            $stmt->close();
            if (password_verify($current_password, $hashed_password)) {
                $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->bind_param("si", $new_hashed_password, $userId);
                if ($stmt->execute()) {
                    $success = "Password updated successfully.";
                } else {
                    $errors[] = "Error updating password.";
                }
                $stmt->close();
            } else {
                $errors[] = "Incorrect current password.";
            }
        }
    }
}
include 'header.php';
?>
<div class="w-full max-w-md p-8 mx-auto bg-white rounded-xl shadow-lg space-y-6">
    <h1 class="text-2xl font-extrabold text-gray-900 text-center">Change Password</h1>
    <?php if (!empty($success)): ?>
        <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
            <?= $success ?>
        </div>
    <?php endif; ?>
    <?php if (!empty($errors)): ?>
        <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
            <ul>
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <form method="POST">
        <?php echo get_csrf_input_field(); ?>
        <div>
            <label class="block text-sm font-medium text-gray-700">Current Password</label>
            <input name="current_password" type="password" required class="w-full px-3 py-2 border rounded">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">New Password</label>
            <input name="new_password" type="password" required class="w-full px-3 py-2 border rounded">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Confirm New Password</label>
            <input name="confirm_new_password" type="password" required class="w-full px-3 py-2 border rounded">
        </div>
        <div class="pt-4">
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">Change Password</button>
        </div>
    </form>
</div>
<?php include './includes/footer.php'; ?>