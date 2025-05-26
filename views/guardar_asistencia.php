<?php
session_start();
require_once __DIR__ . '/../config/conexion.php';

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'tutor') {
    header("Location: login.php");
    exit();
}

$codigoTutor = $_SESSION['codigo_tutor'] ?? null;
if (!$codigoTutor) {
    echo "No se encontró el código de tutor en sesión.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $asistencias = $_POST['asistencia'] ?? [];
    $aspectos = $_POST['aspecto'] ?? [];
    $tipos = $_POST['tipo'] ?? [];

    // Validar que el tutor tiene grupo
    $sqlGrupo = "SELECT id_grupo FROM grupos WHERE codigo_tutor = ?";
    $stmtGrupo = $conn->prepare($sqlGrupo);
    $stmtGrupo->bind_param("s", $codigoTutor);
    $stmtGrupo->execute();
    $resultGrupo = $stmtGrupo->get_result();
    $grupo = $resultGrupo->fetch_assoc();

    if (!$grupo) {
        echo "No tienes un grupo asignado.";
        exit();
    }

    $fechaHoy = date('Y-m-d');
    $idGrupo = $grupo['id_grupo'];

    foreach ($asistencias as $codigoEstudiante => $asistencia) {
        $aspecto = $aspectos[$codigoEstudiante] ?? null;
        $tipo = $tipos[$codigoEstudiante] ?? null;

        // Verificar si ya existe registro de asistencia para esa fecha
        $sqlCheck = "SELECT id_asistencia FROM asistencias WHERE codigo_estudiante = ? AND id_grupo = ? AND fecha = ?";
        $stmtCheck = $conn->prepare($sqlCheck);
        $stmtCheck->bind_param("sis", $codigoEstudiante, $idGrupo, $fechaHoy);
        $stmtCheck->execute();
        $resultCheck = $stmtCheck->get_result();

        if ($resultCheck->num_rows > 0) {
            // Actualizar registro existente
            $row = $resultCheck->fetch_assoc();
            $sqlUpdate = "UPDATE asistencias SET tipo = ?, codigo_tutor = ? WHERE id_asistencia = ?";
            $stmtUpdate = $conn->prepare($sqlUpdate);
            $stmtUpdate->bind_param("ssi", $asistencia, $codigoTutor, $row['id_asistencia']);
            $stmtUpdate->execute();

            // Actualizar aspecto en tabla aspecto_estudiante
            if ($aspecto) {
                // Verificar si ya tiene registro hoy
                $sqlCheckAsp = "SELECT id FROM aspecto_estudiante WHERE codigo_estudiante = ? AND id_aspecto = ? AND fecha = ?";
                $stmtCheckAsp = $conn->prepare($sqlCheckAsp);
                $stmtCheckAsp->bind_param("sis", $codigoEstudiante, $aspecto, $fechaHoy);
                $stmtCheckAsp->execute();
                $resultCheckAsp = $stmtCheckAsp->get_result();

                if ($resultCheckAsp->num_rows > 0) {
                    // ya existe, no hacemos nada o podrías actualizar si quieres
                } else {
                    // Insertar nuevo aspecto asignado
                    $sqlInsertAsp = "INSERT INTO aspecto_estudiante (id_aspecto, fecha, codigo_estudiante, codigo_tutor) VALUES (?, ?, ?, ?)";
                    $stmtInsertAsp = $conn->prepare($sqlInsertAsp);
                    $stmtInsertAsp->bind_param("isss", $aspecto, $fechaHoy, $codigoEstudiante, $codigoTutor);
                    $stmtInsertAsp->execute();
                }
            }
        } else {
            // Insertar nuevo registro de asistencia
            $sqlInsert = "INSERT INTO asistencias (fecha, codigo_estudiante, codigo_tutor, tipo) VALUES (?, ?, ?, ?)";
            $stmtInsert = $conn->prepare($sqlInsert);
            $stmtInsert->bind_param("ssss", $fechaHoy, $codigoEstudiante, $codigoTutor, $asistencia);
            $stmtInsert->execute();

            // Insertar aspecto si existe
            if ($aspecto) {
                $sqlInsertAsp = "INSERT INTO aspecto_estudiante (id_aspecto, fecha, codigo_estudiante, codigo_tutor) VALUES (?, ?, ?, ?)";
                $stmtInsertAsp = $conn->prepare($sqlInsertAsp);
                $stmtInsertAsp->bind_param("isss", $aspecto, $fechaHoy, $codigoEstudiante, $codigoTutor);
                $stmtInsertAsp->execute();
            }
        }
    }

    header("Location: dashboard_tutor.php?msg=asistencia_guardada");
    exit();
}
?>
