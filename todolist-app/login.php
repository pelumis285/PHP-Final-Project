<?php
// login.php - The login page.
require_once './includes/db_config.php';
require_once './includes/csrf.php';
safe_session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        $errors[] = "Invalid request (CSRF).";
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $errors[] = "Email and password are required.";
        } else {
            $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->bind_result($user_id, $hashed_password);
            $stmt->fetch();
            $stmt->close();

            if ($user_id && password_verify($password, $hashed_password)) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user_id;
                header("Location: dashboard.php");
                exit();
            } else {
                $errors[] = "Invalid email or password.";
            }
        }
    }
}
include './includes/header.php';
?>
<div class="w-full max-w-md p-8 mx-auto bg-white rounded-xl shadow-lg space-y-6">
    <h1 class="text-2xl font-extrabold text-gray-900 text-center">Login</h1>
    <?php if (!empty($errors)): ?>
        <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
            <ul>
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <form method="POST" action="login.php">
        <?php echo get_csrf_input_field(); ?>
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input id="email" name="email" type="email" required class="w-full px-3 py-2 border rounded">
        </div>
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <input id="password" name="password" type="password" required class="w-full px-3 py-2 border rounded">
        </div>
        <div class="pt-4">
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">Login</button>
            <a href="register.php" class="ml-4 text-indigo-600">Register</a>
        </div>
    </form>
</div>
<?php include './includes/footer.php'; ?>