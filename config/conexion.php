<?php
// app/models/conexion.php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "academia_sabatina";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
