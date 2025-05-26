<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Carga de modelos al inicio
require_once '../models/tutorModelo.php';
require_once '../models/grupoModelo.php';
require_once '../models/estudianteModelo.php';

// Obtener datos para los selects y tabla
$tutores = obtenerTutores();
$grupos = obtenerGruposConTutor();
$estudiantes = obtenerEstudiantes();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Grupos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../Public/css/style_admin.css">
    <style>
        .input-editar {
            display: none;
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="text-end mb-3">
            <p class="text-muted">Conectado como <strong><?php echo htmlspecialchars($_SESSION['usuario']); ?></strong></p>
            <a href="logout.php" class="btn btn-outline-danger btn-sm">Cerrar sesión</a>
        </div>

        <h2 class="mb-4 text-center">Gestión de Grupos</h2>

        <!-- Crear nuevo grupo -->
        <div class="card p-4 mb-5 shadow-sm">
            <h4 class="mb-3">Crear Nuevo Grupo</h4>
            <form action="../controllers/grupoControlador.php" method="POST">
                <input type="hidden" name="accion" value="crear">

                <div class="mb-3">
                    <label for="nombre_grupo" class="form-label">Nombre del Grupo:</label>
                    <input type="text" name="nombre_grupo" id="nombre_grupo" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="codigo_tutor" class="form-label">Seleccionar Tutor:</label>
                    <select name="codigo_tutor" id="codigo_tutor" class="form-select" required>
                        <?php
                        if ($tutores) {
                            foreach ($tutores as $tutor) {
                                $codigo_tutor = htmlspecialchars($tutor['codigo_tutor']);
                                $nombre = htmlspecialchars($tutor['nombre_completo']);
                                echo "<option value='{$codigo_tutor}'>{$nombre}</option>";
                            }
                        }
                        ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Crear Grupo</button>
            </form>
        </div>
 <!-- Lista de grupos existentes -->
        <div class="card p-4 mb-5 shadow-sm">
            <h4 class="mb-3">Grupos Existentes</h4>
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Tutor</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($grupos) {
                            foreach ($grupos as $grupo) {
                                $id_grupo = (int)$grupo['id_grupo'];
                                $nombre_grupo = htmlspecialchars($grupo['nombre_grupo']);
                                $nombre_tutor = htmlspecialchars($grupo['nombre_tutor']);
                                ?>
                                <tr>
                                    <td><?php echo $id_grupo; ?></td>
                                    <td>
                                        <span class="nombre-label"><?php echo $nombre_grupo; ?></span>
                                    </td>
                                    <td><?php echo $nombre_tutor; ?></td>
                                    <td>
                                        <form method="POST" action="../controllers/grupoControlador.php" onsubmit="return confirm('¿Eliminar grupo?')" class="d-inline">
                                            <input type="hidden" name="accion" value="eliminar">
                                            <input type="hidden" name="id_grupo" value="<?php echo $id_grupo; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo "<tr><td colspan='4' class='text-center'>No hay grupos registrados.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Asignar estudiantes a grupo -->
        <div class="card p-4 mb-5 shadow-sm">
            <h4 class="mb-3">Asignar Estudiantes a Grupo</h4>
            <form action="../controllers/grupoControlador.php" method="POST">
                <input type="hidden" name="accion" value="asignar_estudiantes">

                <div class="mb-3">
                    <label for="id_grupo" class="form-label">Grupo:</label>
                    <select name="id_grupo" id="id_grupo" class="form-select" required>
                        <?php
                        if ($grupos) {
                            foreach ($grupos as $grupo) {
                                $id_grupo = (int)$grupo['id_grupo'];
                                $nombre_grupo = htmlspecialchars($grupo['nombre_grupo']);
                                $nombre_tutor = htmlspecialchars($grupo['nombre_tutor']);
                                echo "<option value='{$id_grupo}'>{$nombre_grupo} (Tutor: {$nombre_tutor})</option>";
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="estudiantes" class="form-label">Seleccionar Estudiantes:</label>
                    <select name="codigo_estudiante[]" id="estudiantes" class="form-select" multiple required size="6">
                        <?php
                        if ($estudiantes) {
                            foreach ($estudiantes as $est) {
                                $codigo_estudiante = htmlspecialchars($est['codigo_estudiante']);
                                $nombre_completo = htmlspecialchars($est['nombres'] . ' ' . $est['apellidos']);
                                echo "<option value='{$codigo_estudiante}'>{$nombre_completo} - {$codigo_estudiante}</option>";
                            }
                        }
                        ?>
                    </select>
                    <div class="form-text">Usa Ctrl (Cmd en Mac) o Shift para seleccionar múltiples estudiantes.</div>
                </div>

                <button type="submit" class="btn btn-success">Asignar Estudiantes</button>
            </form>
        </div>

        <!-- Ver estudiantes inscritos por grupo -->
        <div class="card p-4 mb-5 shadow-sm">
            <h4 class="mb-3">Estudiantes Inscritos en Grupos</h4>
            <?php
            if ($grupos) {
                foreach ($grupos as $grupo) {
                    $id_grupo = (int)$grupo['id_grupo'];
                    $nombre_grupo = htmlspecialchars($grupo['nombre_grupo']);

                    // Obtener estudiantes de este grupo 
                    $estudiantesGrupo = obtenerEstudiantesPorGrupo($id_grupo);

                    echo "<div class='mb-4'>";
                    echo "<h5>{$nombre_grupo}</h5>";

                    if ($estudiantesGrupo) {
                        echo "<div class='table-responsive'>";
                        echo "<table class='table table-sm table-bordered table-hover'>";
                        echo "<thead><tr><th>Código</th><th>Nombre</th><th>Acción</th></tr></thead><tbody>";

                        foreach ($estudiantesGrupo as $est) {
                            $codigo = htmlspecialchars($est['codigo_estudiante']);
                            $nombre = htmlspecialchars($est['nombres'] . ' ' . $est['apellidos']);
                            ?>
                            <tr>
                                <td><?php echo $codigo; ?></td>
                                <td><?php echo $nombre; ?></td>
                                <td>
                                    <form method="POST" action="../controllers/grupoControlador.php" onsubmit="return confirm('¿Eliminar estudiante de este grupo?')" class="d-inline">
                                        <input type="hidden" name="accion" value="eliminar_estudiante">
                                        <input type="hidden" name="codigo_estudiante" value="<?php echo $codigo; ?>">
                                        <input type="hidden" name="id_grupo" value="<?php echo $id_grupo; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                            <?php
                        }

                        echo "</tbody></table></div>";
                    } else {
                        echo "<p class='text-muted'>No hay estudiantes en este grupo.</p>";
                    }

                    echo "</div>";
                }
            } else {
                echo "<p class='text-muted'>No hay grupos registrados.</p>";
            }
            ?>
            <hr>
            <p class="text-end text-muted"><small>Conectado como <strong><?php echo htmlspecialchars($_SESSION['usuario']); ?></strong></small></p>
        </div>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
