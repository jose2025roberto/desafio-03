<?php
session_start();

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../views/login.php");
    exit();
}

require_once __DIR__ . '/../models/grupoModelo.php';
require_once __DIR__ . '../../config/conexion.php';  

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    // Crear grupo
    if ($accion === 'crear') {
        $nombre_grupo = $_POST['nombre_grupo'] ?? '';
        $codigo_tutor = $_POST['codigo_tutor'] ?? '';

        if ($nombre_grupo && $codigo_tutor) {
            $creado = crearGrupo($nombre_grupo, $codigo_tutor);
            if ($creado) {
                header("Location: ../views/dashboard_admin.php?msg=Grupo creado correctamente");
            } else {
                header("Location: ../views/dashboard_admin.php?error=Error al crear grupo");
            }
        } else {
            header("Location: ../views/dashboard_admin.php?error=Datos incompletos");
        }
        exit();
    }


    // Eliminar grupo
    if ($accion === 'eliminar') {
        $id_grupo = $_POST['id_grupo'] ?? '';

        if ($id_grupo) {
            $eliminado = eliminarGrupo($id_grupo);
            if ($eliminado) {
                header("Location: ../views/dashboard_admin.php?msg=Grupo eliminado correctamente");
            } else {
                header("Location: ../views/dashboard_admin.php?error=Error al eliminar grupo");
            }
        } else {
            header("Location: ../views/dashboard_admin.php?error=ID de grupo inválido");
        }
        exit();
    }

    // Asignar estudiantes al grupo
    if ($accion === 'asignar_estudiantes') {
        $id_grupo = $_POST['id_grupo'] ?? '';
        $estudiantes = $_POST['codigo_estudiante'] ?? [];

        if ($id_grupo && is_array($estudiantes) && count($estudiantes) > 0) {
            foreach ($estudiantes as $codigo_estudiante) {
                $sql_check = "SELECT 1 FROM grupo_estudiantes WHERE id_grupo = ? AND codigo_estudiante = ?";
                $stmt_check = $conn->prepare($sql_check);
                if (!$stmt_check) {
                    header("Location: ../views/dashboard_admin.php?error=Error en la consulta");
                    exit();
                }

                $stmt_check->bind_param("is", $id_grupo, $codigo_estudiante);
                $stmt_check->execute();
                $result_check = $stmt_check->get_result();

                if ($result_check->num_rows === 0) {
                    $sql_insert = "INSERT INTO grupo_estudiantes (id_grupo, codigo_estudiante) VALUES (?, ?)";
                    $stmt_insert = $conn->prepare($sql_insert);
                    if (!$stmt_insert) {
                        $stmt_check->close();
                        header("Location: ../views/dashboard_admin.php?error=Error en la inserción");
                        exit();
                    }
                    $stmt_insert->bind_param("is", $id_grupo, $codigo_estudiante);
                    $stmt_insert->execute();
                    $stmt_insert->close();
                }
                $stmt_check->close();
            }

            header("Location: ../views/dashboard_admin.php?msg=Estudiantes asignados correctamente");
        } else {
            header("Location: ../views/dashboard_admin.php?error=Datos incompletos para asignación");
        }
        exit();
    }

    // Eliminar estudiante del grupo
    if ($accion === 'eliminar_estudiante') {
        $id_grupo = $_POST['id_grupo'] ?? '';
        $codigo_estudiante = $_POST['codigo_estudiante'] ?? '';

        if ($id_grupo && $codigo_estudiante) {
            $eliminado = eliminarEstudianteDeGrupo($id_grupo, $codigo_estudiante);
            if ($eliminado) {
                header("Location: ../views/dashboard_admin.php?msg=Estudiante eliminado del grupo");
            } else {
                header("Location: ../views/dashboard_admin.php?error=Error al eliminar estudiante del grupo");
            }
        } else {
            header("Location: ../views/dashboard_admin.php?error=Datos incompletos para eliminar estudiante");
        }
        exit();
    }
}

// Función para obtener estudiantes por grupo
function obtenerEstudiantesPorGrupo() {
    global $conn;

    $sql = "SELECT ge.id_grupo, g.nombre_grupo, e.codigo_estudiante, e.nombres, e.apellidos
            FROM grupo_estudiantes ge
            JOIN grupos g ON ge.id_grupo = g.id_grupo
            JOIN estudiantes e ON ge.codigo_estudiante = e.codigo_estudiante
            ORDER BY g.nombre_grupo, e.apellidos";

    $result = $conn->query($sql);

    $estudiantesPorGrupo = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $estudiantesPorGrupo[] = $row;
        }
    }
    return $estudiantesPorGrupo;
}

// Función para eliminar estudiante de grupo
function eliminarEstudianteDeGrupo($id_grupo, $codigo_estudiante) {
    global $conn;

    $stmt = $conn->prepare("DELETE FROM grupo_estudiantes WHERE id_grupo = ? AND codigo_estudiante = ?");
    $stmt->bind_param("is", $id_grupo, $codigo_estudiante);
    $resultado = $stmt->execute();
    $stmt->close();

    return $resultado;
}

// Función para actualizar grupo
function actualizarGrupo($id_grupo, $nombre_grupo, $codigo_tutor) {
    global $conn;

    $stmt = $conn->prepare("UPDATE grupos SET nombre_grupo = ?, codigo_tutor = ? WHERE id_grupo = ?");
    if (!$stmt) {
        return false;
    }

    $stmt->bind_param("ssi", $nombre_grupo, $codigo_tutor, $id_grupo);
    $resultado = $stmt->execute();
    $stmt->close();

    return $resultado;
}


