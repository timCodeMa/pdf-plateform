<?php
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../classes/User.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = new User();
    if ($user->create($_POST['email'], $_POST['password'])) {
        header('Location: login.php');
        exit;
    }
}
