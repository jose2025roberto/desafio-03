<?php
require_once __DIR__ . '/../config/conexion.php';

function crearGrupo($nombre_grupo, $codigo_tutor) {
    global $conn;
    $sql = "INSERT INTO grupos (nombre_grupo, codigo_tutor) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Error en preparar crearGrupo: " . $conn->error);
        return false;
    }
    $stmt->bind_param("ss", $nombre_grupo, $codigo_tutor);
    $resultado = $stmt->execute();
    $stmt->close();
    return $resultado;
}

function eliminarGrupo($id_grupo) {
    global $conn;
    $sql = "DELETE FROM grupos WHERE id_grupo = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Error en preparar eliminarGrupo: " . $conn->error);
        return false;
    }
    $stmt->bind_param("i", $id_grupo);
    $resultado = $stmt->execute();
    $stmt->close();
    return $resultado;
}

function obtenerGruposConTutor() {
    global $conn;
    $sql = "SELECT g.id_grupo, g.nombre_grupo, CONCAT(t.nombres, ' ', t.apellidos) AS nombre_tutor
            FROM grupos g
            LEFT JOIN tutores t ON g.codigo_tutor = t.codigo_tutor";
    $result = $conn->query($sql);
    if (!$result) {
        die("Error al obtener grupos: " . $conn->error);
    }
    return $result->fetch_all(MYSQLI_ASSOC);
}


