<?php
// users.php - List of registered users with edit & delete options.
require_once './includes/db_config.php';
require_once './includes/csrf.php';
safe_session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$users = [];
$stmt = $conn->prepare("SELECT id, username, email, image_path FROM users ORDER BY username ASC");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}
$stmt->close();

include './includes/header.php';
?>
<div class="w-full max-w-4xl p-8 mx-auto bg-white rounded-xl shadow-lg space-y-6">
    <h1 class="text-3xl font-bold text-gray-800 text-center">All Users</h1>
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Avatar
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Username
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Email
                </th>
                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Actions
                </th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php foreach ($users as $user): ?>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <?php if ($user['image_path']): ?>
                            <img src="<?= htmlspecialchars($user['image_path']) ?>" alt="User Avatar" class="h-10 w-10 rounded-full object-cover">
                        <?php else: ?>
                            <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 font-bold">
                                <?= strtoupper(substr($user['username'], 0, 1)) ?>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        <?= htmlspecialchars($user['username']) ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <?= htmlspecialchars($user['email']) ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="edit_user.php?id=<?= $user['id'] ?>" class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</a>
                        <form action="delete_user.php" method="POST" class="inline-block">
                            <?php echo get_csrf_input_field(); ?>
                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this user?');">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php
include './includes/footer.php';
?>