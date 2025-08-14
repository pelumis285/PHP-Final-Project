<?php
// header.php
// This file contains the global header with a logo, navigation, and login/logout links.

require_once 'csrf.php';

safe_session_start();

$is_logged_in = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do App</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
        }
    </style>
</head>
<body class="bg-gray-100">
    <header class="bg-white shadow-md">
        <nav class="container mx-auto flex items-center justify-between p-4">
            <a href="index.php" class="text-2xl font-bold text-gray-800">
                <span class="text-indigo-600">Todo</span>App
            </a>
            <div class="flex items-center space-x-6">
                <a href="index.php" class="text-gray-600 hover:text-indigo-600 transition-colors">Home</a>
                <a href="about.php" class="text-gray-600 hover:text-indigo-600 transition-colors">About</a>
                <?php if ($is_logged_in): ?>
                    <a href="dashboard.php" class="text-gray-600 hover:text-indigo-600 transition-colors">Dashboard</a>
                    <a href="users.php" class="text-gray-600 hover:text-indigo-600 transition-colors">Users</a>
                <?php endif; ?>
            </div>
            <div>
                <?php if ($is_logged_in): ?>
                    <form action="../logout.php" method="POST">
                        <?php echo get_csrf_input_field(); ?>
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-medium py-2 px-4 rounded-md shadow-md transition-colors">
                            Logout
                        </button>
                    </form>
                <?php else: ?>
                    <a href="login.php" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-md shadow-md transition-colors">
                        Login
                    </a>
                <?php endif; ?>
            </div>
        </nav>
    </header>
    <main class="container mx-auto p-4">