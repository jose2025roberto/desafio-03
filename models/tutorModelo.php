<?php
require_once __DIR__ . '/../config/conexion.php';

function obtenerTutores() {
    global $conn;
    // Concatenar nombres y apellidos como nombre_completo
    $sql = "SELECT codigo_tutor, CONCAT(nombres, ' ', apellidos) AS nombre_completo FROM tutores";
    $result = $conn->query($sql);
    if ($result) {
        return $result->fetch_all(MYSQLI_ASSOC);
    } else {
        return [];
    }
}
