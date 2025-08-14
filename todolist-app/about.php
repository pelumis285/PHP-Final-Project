<?php
// about.php - An about page for the application.
require_once './includes/csrf.php';
safe_session_start();
include './includes/header.php';
?>
<div class="p-8 bg-white rounded-xl shadow-md">
    <h1 class="text-3xl font-bold text-gray-800">About Our App</h1>
    <p class="mt-4 text-gray-600">
        ToDoApp is your go-to web application for effortlessly organizing and managing your daily tasks. Designed with simplicity and efficiency in mind, it empowers users to create, track, and complete their to-do lists with ease, ensuring you stay on top of your responsibilities. Whether you're juggling work, personal projects, or household chores, ToDoApp provides a streamlined and intuitive platform to keep everything in order.
    </p>
    <p class="mt-4 text-gray-600">
        Our mission is to help you boost productivity and reduce stress by offering a clean, user-friendly interface that adapts to your needs. Built with modern web technologies, ToDoApp delivers a responsive experience across devices, making task management accessible anytime, anywhere. We are committed to continuous improvement, ensuring ToDoApp remains a reliable tool for individuals seeking to simplify their lives through effective task organization.
    </p>
</div>
<?php
include './includes/footer.php';
?>