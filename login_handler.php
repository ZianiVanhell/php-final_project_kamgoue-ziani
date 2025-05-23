<?php
session_start();
require_once 'User.php';

try {
    $user = new User($_POST['login']);
    $user->login($_POST['password']);
    header("Location: dashboard.php");
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>