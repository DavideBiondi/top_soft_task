<?php
require_once __DIR__ . '/secrets.php';
$host="mysql-fcc-db-fcc-webapp-python-flask.k.aivencloud.com";
$user="avnadmin";
$pass=$password_db;
$db="topsoft_task";
$port=$port_db;

$conn= new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    # code...
    die("Connessione fallita: " . $conn->connect_error);
}

?>