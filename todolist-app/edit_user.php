<?php
// edit_user.php - Edit a registered user's details (requires login).
require_once './includes/db_config.php';
require_once './includes/csrf.php';
safe_session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$errors = [];
$success = '';
$user = null;

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);

if ($id > 0) {
    $stmt = $conn->prepare("SELECT id, username, email, image_path FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
}

if (!$user) {
    header("Location: users.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        $errors[] = "Invalid request (CSRF).";
    } else {
        $username = htmlspecialchars(trim($_POST['username']));
        $email = htmlspecialchars(trim($_POST['email']));
        $image_file = $_FILES['user_image'];

        if (empty($username) || empty($email)) {
            $errors[] = "Username and email are required.";
        }

        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->bind_param("si", $email, $id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "Another account is already using that email.";
        }
        $stmt->close();

        $image_path = $user['image_path'];
        if (isset($image_file) && $image_file['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $file_extension = strtolower(pathinfo($image_file['name'], PATHINFO_EXTENSION));
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array($file_extension, $allowed_extensions)) {
                if ($user['image_path'] && file_exists($user['image_path'])) {
                    @unlink($user['image_path']);
                }
                $new_filename = uniqid('user_') . '.' . $file_extension;
                $destination = $upload_dir . $new_filename;
                if (move_uploaded_file($image_file['tmp_name'], $destination)) {
                    $image_path = $destination;
                } else {
                    $errors[] = "Error uploading image.";
                }
            } else {
                $errors[] = "Invalid image file type.";
            }
        }

        if (empty($errors)) {
            if ($image_path) {
                $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, image_path = ? WHERE id = ?");
                $stmt->bind_param("sssi", $username, $email, $image_path, $id);
            } else {
                $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
                $stmt->bind_param("ssi", $username, $email, $id);
            }
            if ($stmt->execute()) {
                $success = "User updated successfully.";
                $user['username'] = $username;
                $user['email'] = $email;
                $user['image_path'] = $image_path;
            } else {
                $errors[] = "Error updating user: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}
include 'header.php';
?>
<div class="w-full max-w-md p-8 mx-auto bg-white rounded-xl shadow-lg space-y-6">
    <h1 class="text-2xl font-extrabold text-gray-900 text-center">Edit User</h1>
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
    <form method="POST" enctype="multipart/form-data">
        <?php echo get_csrf_input_field(); ?>
        <input type="hidden" name="id" value="<?= htmlspecialchars($user['id']) ?>">
        <div>
            <label class="block text-sm font-medium text-gray-700">Username</label>
            <input name="username" value="<?= htmlspecialchars($user['username']) ?>" required class="w-full px-3 py-2 border rounded">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Email</label>
            <input name="email" type="email" value="<?= htmlspecialchars($user['email']) ?>" required class="w-full px-3 py-2 border rounded">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Current Avatar</label>
            <?php if ($user['image_path']): ?>
                <img src="<?= htmlspecialchars($user['image_path']) ?>" alt="User Avatar" class="w-24 h-24 rounded-full object-cover mt-2">
            <?php else: ?>
                <div class="mt-2 text-sm text-gray-500">No avatar uploaded.</div>
            <?php endif; ?>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Replace Avatar</label>
            <input name="user_image" type="file" class="w-full px-3 py-2 border rounded">
        </div>
        <div class="pt-4">
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">Save Changes</button>
            <a href="users.php" class="ml-4 text-gray-600">Cancel</a>
        </div>
    </form>
</div>
<?php include './includes/footer.php'; ?>