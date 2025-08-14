<?php
// index.php - The main home page.
require_once './includes/csrf.php';
safe_session_start();
include './includes/header.php';
?>
<div class="text-center p-12 bg-gray-200 rounded-xl shadow-md">
    <h1 class="text-5xl font-extrabold text-gray-900">Welcome to TodoApp</h1>
    <p class="mt-4 text-lg text-gray-600">Your simple and effective to-do list manager.</p>
    <a href="register.php" class="mt-8 inline-block bg-indigo-600 text-white text-lg font-semibold py-3 px-8 rounded-full shadow-lg hover:bg-indigo-700 transition duration-300">
        Get Started
    </a>
</div>
<?php
include './includes/footer.php';
?>