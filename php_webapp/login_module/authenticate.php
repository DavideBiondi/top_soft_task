<?php 
// This file receives data from a form, checks whether an admin and a password exists, 
// and creates the session

session_start();
require_once "db_connect.php";

$username=$_POST['username'];
$password=$_POST['password'];

// Query to check the db
$stmt= $conn->prepare("SELECT * FROM admin WHERE username = ? AND password = ?");
$stmt->bind_param("ss", $username, $password);
$stmt->execute();
$result=$stmt->get_result();

if ($result->num_rows === 1) {
    # code...
    $_SESSION['loggedin'] = true;
    $_SESSION['username'] = $username;
    header("Location: dashboard.php");
    exit;
}else{
    $_SESSION['error'] = "Credenziali non valide!";
    header("Location: login.php");
    exit;
}
?>