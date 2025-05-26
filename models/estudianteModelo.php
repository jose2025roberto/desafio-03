<?php

require_once '../config/conexion.php';

// Obtener todos los estudiantes activos
function obtenerEstudiantes() {
    global $conn;

    $sql = "SELECT codigo_estudiante, nombres, apellidos 
            FROM estudiantes 
            WHERE estado = 'activo' 
            ORDER BY apellidos, nombres";

    $resultado = $conn->query($sql);
    $estudiantes = [];

    if ($resultado && $resultado->num_rows > 0) {
        while ($fila = $resultado->fetch_assoc()) {
            $estudiantes[] = $fila;
        }
    }

    return $estudiantes;
}

// Obtener estudiantes asignados a un grupo específico
function obtenerEstudiantesPorGrupo($id_grupo) {
    global $conn;

    $sql = "SELECT e.codigo_estudiante, e.nombres, e.apellidos
            FROM estudiantes e
            INNER JOIN grupo_estudiantes ge ON e.codigo_estudiante = ge.codigo_estudiante
            WHERE ge.id_grupo = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_grupo);
    $stmt->execute();

    $resultado = $stmt->get_result();
    $estudiantes = [];

    if ($resultado && $resultado->num_rows > 0) {
        while ($fila = $resultado->fetch_assoc()) {
            $estudiantes[] = $fila;
        }
    }

    $stmt->close();
    return $estudiantes;
}
