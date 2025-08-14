<?php
// dashboard.php - The main page for logged-in users.
require_once './includes/db_config.php';
require_once './includes/csrf.php';
safe_session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$userId = $_SESSION['user_id'];
$success = '';

function redirect_with_message($message, $type = 'success') {
    safe_session_start();
    $_SESSION['message'] = ['text' => $message, 'type' => $type];
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_task'])) {
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        die("Invalid request (CSRF).");
    }
    $newTask = htmlspecialchars(trim($_POST['new_task']));
    if (!empty($newTask)) {
        $stmt = $conn->prepare("INSERT INTO tasks (task_name, user_id) VALUES (?, ?)");
        $stmt->bind_param("si", $newTask, $userId);
        $stmt->execute();
        $stmt->close();
        redirect_with_message("Task added successfully!");
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_task_id'])) {
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        die("Invalid request (CSRF).");
    }
    $taskId = (int)$_POST['update_task_id'];
    $isCompleted = isset($_POST['is_completed']) ? 1 : 0;
    $stmt = $conn->prepare("UPDATE tasks SET is_completed = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("iii", $isCompleted, $taskId, $userId);
    $stmt->execute();
    $stmt->close();
    redirect_with_message("Task updated successfully!");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_task_id'])) {
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        die("Invalid request (CSRF).");
    }
    $taskId = (int)$_POST['delete_task_id'];
    $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $taskId, $userId);
    $stmt->execute();
    $stmt->close();
    redirect_with_message("Task deleted successfully!");
}

$tasks = [];
$stmt = $conn->prepare("SELECT id, task_name, is_completed FROM tasks WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $tasks[] = $row;
    }
}
$stmt->close();
$conn->close();

include './includes/header.php';

if (isset($_SESSION['message'])) {
    $success = $_SESSION['message']['text'];
    unset($_SESSION['message']);
}
?>
<div class="w-full max-w-2xl p-8 mx-auto bg-white rounded-xl shadow-lg space-y-6">
    <h1 class="text-4xl font-extrabold text-center text-gray-900">
        My To-Do List
    </h1>
    <?php if (!empty($success)): ?>
        <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>
    <form action="dashboard.php" method="POST" class="flex items-center space-x-4">
        <?php echo get_csrf_input_field(); ?>
        <input type="text" name="new_task" placeholder="Add a new task..." required
               class="flex-grow px-4 py-2 text-lg border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <button type="submit"
                class="px-6 py-2 text-lg font-medium text-white bg-indigo-600 rounded-lg shadow-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
            Add
        </button>
    </form>
    <ul class="space-y-4">
        <?php if (empty($tasks)): ?>
            <li class="p-4 text-center text-gray-500 rounded-lg border border-dashed">
                No tasks yet! Add one above.
            </li>
        <?php else: ?>
            <?php foreach ($tasks as $task): ?>
                <li class="flex items-center justify-between p-4 bg-gray-100 rounded-lg shadow-sm transition-transform hover:scale-[1.01]">
                    <form action="dashboard.php" method="POST" class="flex-grow flex items-center">
                        <?php echo get_csrf_input_field(); ?>
                        <input type="hidden" name="update_task_id" value="<?= $task['id'] ?>">
                        <label class="flex items-center space-x-3 cursor-pointer">
                            <input type="checkbox" name="is_completed" onchange="this.form.submit()"
                                   <?= $task['is_completed'] ? 'checked' : '' ?>
                                   class="h-5 w-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                            <span class="text-xl font-medium <?= $task['is_completed'] ? 'line-through text-gray-600' : 'text-gray-800' ?>">
                                <?= htmlspecialchars($task['task_name']) ?>
                            </span>
                        </label>
                    </form>
                    <form action="dashboard.php" method="POST">
                        <?php echo get_csrf_input_field(); ?>
                        <input type="hidden" name="delete_task_id" value="<?= $task['id'] ?>">
                        <button type="submit"
                                class="text-red-500 hover:text-red-700 transition duration-150 ease-in-out">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </form>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>
</div>
<?php
include './includes/footer.php';
?>