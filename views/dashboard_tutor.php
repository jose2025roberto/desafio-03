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

// Restringir acceso solo sábados de 8:00am a 11:00am
date_default_timezone_set('America/El_Salvador');
$diaSemana = date('N'); // 6 = sábado
$horaActual = date('H:i');

if ($diaSemana != 6 || $horaActual < '08:00' || $horaActual > '11:00') {
    echo "<!DOCTYPE html>";
    echo "<html lang='es'>";
    echo "<head>";
    echo "<meta charset='UTF-8'>";
    echo "<title>Acceso Restringido</title>";
    echo "<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>";
    echo "</head>";
    echo "<body>";
    echo "<div class='container mt-4'>";
    echo "<div class='alert alert-warning'>La toma de asistencia solo está habilitada los sábados de 8:00am a 11:00am.</div>";
    echo "<div class='d-flex gap-3'>";
    echo "<form action='logout.php' method='POST'>";
    echo "<button type='submit' class='btn btn-danger'>Cerrar sesión</button>";
    echo "</form>";
    echo "</div>";
    echo "</div>";
    echo "</body>";
    echo "</html>";
    exit();
}


// Obtener grupo asignado al tutor
$sqlGrupo = "SELECT id_grupo, nombre_grupo FROM grupos WHERE codigo_tutor = ?";
$stmtGrupo = $conn->prepare($sqlGrupo);
$stmtGrupo->bind_param("s", $codigoTutor);
$stmtGrupo->execute();
$resultGrupo = $stmtGrupo->get_result();
$grupo = $resultGrupo->fetch_assoc();

if (!$grupo) {
    echo "No tienes un grupo asignado.";
    exit();
}

// Obtener nombre del tutor
$sqlTutor = "SELECT nombres, apellidos FROM tutores WHERE codigo_tutor = ?";
$stmtTutor = $conn->prepare($sqlTutor);
$stmtTutor->bind_param("s", $codigoTutor);
$stmtTutor->execute();
$resultTutor = $stmtTutor->get_result();
$tutor = $resultTutor->fetch_assoc();

$nombreTutor = $tutor ? $tutor['nombres'] . ' ' . $tutor['apellidos'] : 'Tutor';

// Procesar formulario POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $asistencias = $_POST['asistencia'] ?? [];
    $tipos = $_POST['tipo'] ?? [];
    $aspectos = $_POST['aspecto'] ?? [];
    $fechaHoy = date('Y-m-d');

    foreach ($asistencias as $codigoEstudiante => $asistencia) {
        $tipo = $tipos[$codigoEstudiante] ?? null;
        $aspecto = $aspectos[$codigoEstudiante] ?? null;

        // Verificar si ya existe la asistencia
        $sqlCheck = "SELECT id_asistencia FROM asistencias WHERE codigo_estudiante = ? AND fecha = ?";
        $stmtCheck = $conn->prepare($sqlCheck);
        $stmtCheck->bind_param("ss", $codigoEstudiante, $fechaHoy);
        $stmtCheck->execute();
        $resultCheck = $stmtCheck->get_result();

        if ($resultCheck->num_rows > 0) {
            $row = $resultCheck->fetch_assoc();
            $sqlUpdate = "UPDATE asistencias SET tipo = ?, codigo_tutor = ? WHERE id_asistencia = ?";
            $stmtUpdate = $conn->prepare($sqlUpdate);
            $stmtUpdate->bind_param("ssi", $tipo, $codigoTutor, $row['id_asistencia']);
            $stmtUpdate->execute();
        } else {
            $sqlInsert = "INSERT INTO asistencias (fecha, codigo_estudiante, codigo_tutor, tipo) VALUES (?, ?, ?, ?)";
            $stmtInsert = $conn->prepare($sqlInsert);
            $stmtInsert->bind_param("ssss", $fechaHoy, $codigoEstudiante, $codigoTutor, $tipo);
            $stmtInsert->execute();
        }

        // Insertar aspecto (si hay)
        if (!empty($aspecto)) {
            $sqlInsertAspecto = "INSERT INTO aspecto_estudiante (id_aspecto, fecha, codigo_estudiante, codigo_tutor) VALUES (?, ?, ?, ?)";
            $stmtAspecto = $conn->prepare($sqlInsertAspecto);
            $stmtAspecto->bind_param("isss", $aspecto, $fechaHoy, $codigoEstudiante, $codigoTutor);
            $stmtAspecto->execute();
        }
    }

    header("Location: dashboard_tutor.php?msg=guardado");
    exit();
}

// Obtener estudiantes
$sqlEstudiantes = "
    SELECT e.codigo_estudiante, e.nombres, e.apellidos
    FROM estudiantes e
    INNER JOIN grupo_estudiantes ge ON e.codigo_estudiante = ge.codigo_estudiante
    WHERE ge.id_grupo = ?
    ORDER BY e.apellidos, e.nombres
";

$stmtEstudiantes = $conn->prepare($sqlEstudiantes);
$stmtEstudiantes->bind_param("i", $grupo['id_grupo']);
$stmtEstudiantes->execute();
$resultEstudiantes = $stmtEstudiantes->get_result();

// Obtener aspectos
$sqlAspectos = "SELECT id_aspecto, descripcion, tipo FROM aspectos ORDER BY tipo";
$resultAspectos = $conn->query($sqlAspectos);

$aspectosDisponibles = [];
while ($row = $resultAspectos->fetch_assoc()) {
    $aspectosDisponibles[] = $row;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Dashboard Tutor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Bienvenido Tutor: <strong><?= htmlspecialchars($nombreTutor) ?></strong> - Grupo: <strong><?= htmlspecialchars($grupo['nombre_grupo']) ?></strong></h2>
        <form action="logout.php" method="POST" class="m-0">
            <button type="submit" class="btn btn-danger">Cerrar Sesión</button>
        </form>
    </div>

    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'guardado'): ?>
        <div class="alert alert-success" role="alert">
            Asistencias y aspectos guardados correctamente.
        </div>
    <?php endif; ?>

    <form method="POST" action="dashboard_tutor.php">
        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Código</th>
                        <th>Nombre Completo</th>
                        <th>Asistencia</th>
                        <th>Tipo</th>
                        <th>Aspecto</th>
                        <th>Reporte Trimestral</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($estudiante = $resultEstudiantes->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($estudiante['codigo_estudiante']) ?></td>
                            <td><?= htmlspecialchars($estudiante['apellidos'] . ', ' . $estudiante['nombres']) ?></td>
                            <td>
                                <select name="asistencia[<?= $estudiante['codigo_estudiante'] ?>]" class="form-select" required>
                                    <option value="A">Asistió</option>
                                    <option value="I">No Asistió</option>
                                    <option value="J">Justificado</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" name="tipo[<?= $estudiante['codigo_estudiante'] ?>]" placeholder="Tipo (opcional)" class="form-control" />
                            </td>
                            <td>
                                <select name="aspecto[<?= $estudiante['codigo_estudiante'] ?>]" class="form-select">
                                    <option value="">-- Seleccionar --</option>
                                    <?php foreach ($aspectosDisponibles as $aspecto): ?>
                                        <option value="<?= $aspecto['id_aspecto'] ?>">
                                            <?= htmlspecialchars($aspecto['descripcion']) ?> (<?= $aspecto['tipo'] ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td class="text-center">
                                <a href="ver_pdf_trimestral.php?codigo=<?= urlencode($estudiante['codigo_estudiante']) ?>" class="btn btn-outline-secondary btn-sm" target="_blank">
                                    Ver PDF
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="text-center mt-4">
            <button type="submit" class="btn btn-primary">Guardar Asistencias y Aspectos</button>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>  