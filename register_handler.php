<?php
session_start();
require_once 'User.php';

try {
    $user = new User($_POST['login']);
    $user->register($_POST['password']);
    echo "Registration successful! <a href='login.php'>Login now</a>";
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>