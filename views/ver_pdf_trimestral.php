<?php
session_start();
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '../../../vendor/autoload.php'; 
require_once __DIR__ . '../../../vendor/setasign/fpdf/fpdf.php';

// Validar sesión (opcional)
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'tutor') {
    header("Location: login.php");
    exit();
}

$codigoEstudiante = $_GET['codigo'] ?? '';

if (empty($codigoEstudiante)) {
    die("Código de estudiante no especificado.");
}

$codigoTutor = $_SESSION['codigo_tutor'] ?? null;

if ($codigoTutor) {
    // Tutor
    $sqlTutor = "SELECT CONCAT(nombres, ' ', apellidos) AS nombre_completo FROM tutores WHERE codigo_tutor = ?";
    $stmtTutor = $conn->prepare($sqlTutor);
    $stmtTutor->bind_param("s", $codigoTutor);
    $stmtTutor->execute();
    $tutor = $stmtTutor->get_result()->fetch_assoc();
    $tutorNombre = $tutor['nombre_completo'] ?? 'Nombre Tutor';

    // Grupo
    $sqlGrupo = "SELECT nombre_grupo FROM grupos WHERE codigo_tutor = ?";
    $stmtGrupo = $conn->prepare($sqlGrupo);
    $stmtGrupo->bind_param("s", $codigoTutor);
    $stmtGrupo->execute();
    $grupo = $stmtGrupo->get_result()->fetch_assoc();
    $grupoNombre = $grupo['nombre_grupo'] ?? 'Grupo sin asignar';

} else {
    $tutorNombre = 'Nombre Tutor';
    $grupoNombre = 'Grupo sin asignar';
}

function obtenerTrimestreActual() {
    $mes = date('n');
    if ($mes >= 1 && $mes <= 3) return 1;
    if ($mes >= 4 && $mes <= 6) return 2;
    if ($mes >= 7 && $mes <= 9) return 3;
    return 4;
}

$idTrimestre = obtenerTrimestreActual();

function rangoFechasTrimestre($trimestre, $anio = null) {
    if (!$anio) $anio = date('Y');
    switch ($trimestre) {
        case 1: return ["$anio-01-01", "$anio-03-31"];
        case 2: return ["$anio-04-01", "$anio-06-30"];
        case 3: return ["$anio-07-01", "$anio-09-30"];
        case 4: return ["$anio-10-01", "$anio-12-31"];
        default: return ["$anio-01-01", "$anio-12-31"];
    }
}

list($fechaInicio, $fechaFin) = rangoFechasTrimestre($idTrimestre);

// Obtener datos del estudiante
$sqlEstudiante = "SELECT nombres, apellidos FROM estudiantes WHERE codigo_estudiante = ?";
$stmt = $conn->prepare($sqlEstudiante);
$stmt->bind_param("s", $codigoEstudiante);
$stmt->execute();
$result = $stmt->get_result();
$estudiante = $result->fetch_assoc();

if (!$estudiante) {
    die("Estudiante no encontrado.");
}

// Obtener aspectos en el trimestre
$sqlAspectos = "SELECT a.descripcion, a.tipo, ae.fecha
                FROM aspecto_estudiante ae
                JOIN aspectos a ON ae.id_aspecto = a.id_aspecto
                WHERE ae.codigo_estudiante = ?
                AND ae.fecha BETWEEN ? AND ?
                ORDER BY ae.fecha";
$stmt = $conn->prepare($sqlAspectos);
$stmt->bind_param("sss", $codigoEstudiante, $fechaInicio, $fechaFin);
$stmt->execute();
$result = $stmt->get_result();
$aspectos = $result->fetch_all(MYSQLI_ASSOC);

$aspectosPositivos = [];
$aspectosMejorar = [];

foreach ($aspectos as $aspecto) {
    if (strtoupper($aspecto['tipo']) === 'P') {
        $aspectosPositivos[] = ['fecha' => $aspecto['fecha'], 'descripcion' => $aspecto['descripcion']];
    } else {
        // Los negativos son tipos L, G, MG
        $tipoTexto = match (strtoupper($aspecto['tipo'])) {
            'L' => 'Leve',
            'G' => 'Grave',
            'MG' => 'Muy Grave',
            default => 'Desconocido',
        };
        $aspectosMejorar[] = ['fecha' => $aspecto['fecha'], 'descripcion' => $aspecto['descripcion'], 'tipo' => $tipoTexto];
    }
}

// Obtener inasistencias en el trimestre
$sqlInasistencias = "SELECT fecha, tipo FROM asistencias
                    WHERE codigo_estudiante = ?
                    AND fecha BETWEEN ? AND ?
                    AND tipo IN ('I', 'J')
                    ORDER BY fecha";
$stmt = $conn->prepare($sqlInasistencias);
$stmt->bind_param("sss", $codigoEstudiante, $fechaInicio, $fechaFin);
$stmt->execute();
$result = $stmt->get_result();
$inasistencias = $result->fetch_all(MYSQLI_ASSOC);


// Contar aspectos según tipo para el semáforo
$aspectosPositivosCount = count($aspectosPositivos);
$aspectosLevesCount = 0;
$aspectosGravesCount = 0;
$aspectosMuyGravesCount = 0;

foreach ($aspectosMejorar as $aspecto) {
    switch ($aspecto['tipo']) {
        case 'Leve':
            $aspectosLevesCount++;
            break;
        case 'Grave':
            $aspectosGravesCount++;
            break;
        case 'Muy Grave':
            $aspectosMuyGravesCount++;
            break;
    }
}

// Contar inasistencias injustificadas (tipo 'I')
$inasistenciasInjustificadasCount = 0;
foreach ($inasistencias as $inasistencia) {
    if ($inasistencia['tipo'] === 'I') {
        $inasistenciasInjustificadasCount++;
    }
}

// Función para calcular semáforo
function calcularSemaforo($aspectosPositivos, $aspectosLeves, $inasistenciasInjustificadas, $aspectosGraves, $aspectosMuyGraves) {

    // 1. Evaluar primero el rojo
    if (
        (
            ($aspectosLeves >= 6 || $inasistenciasInjustificadas >= 4 || ($aspectosLeves + $inasistenciasInjustificadas) >= 6)
            && $aspectosGraves >= 1
        ) ||
        $aspectosLeves >= 12 ||
        $inasistenciasInjustificadas >= 8 ||
        $aspectosGraves >= 2 ||
        $aspectosMuyGraves >= 1
    ) {
        return 'red';
    }

    // 2. Luego azul
    if (
        $aspectosPositivos >= 4 &&
        $aspectosLeves <= 1 &&
        $inasistenciasInjustificadas <= 1 &&
        $aspectosGraves == 0 &&
        $aspectosMuyGraves == 0
    ) {
        return 'blue';
    }

    // 3. Después verde
    if (
        $aspectosLeves <= 2 &&
        $inasistenciasInjustificadas <= 2 &&
        $aspectosGraves == 0 &&
        $aspectosMuyGraves == 0
    ) {
        return 'green';
    }

    // 4. Por último amarillo
    if (
        ($aspectosLeves >= 6 || $inasistenciasInjustificadas >= 4 || ($aspectosLeves + $inasistenciasInjustificadas) >= 6) ||
        ($aspectosGraves >= 1 && $aspectosMuyGraves == 0)
    ) {
        return 'yellow';
    }

    return 'green';
}


// Calcular color semáforo
$colorSemaforo = calcularSemaforo(
    $aspectosPositivosCount,
    $aspectosLevesCount,
    $inasistenciasInjustificadasCount,
    $aspectosGravesCount,
    $aspectosMuyGravesCount
);


class PDF extends FPDF
{
     // Método para dibujar círculo relleno
    function Circle($x, $y, $r, $style = '')
    {
        $k = $this->k;
        $hp = $this->h;

        if ($style == 'F')
            $op = 'f';
        elseif ($style == 'FD' || $style == 'DF')
            $op = 'B';
        else
            $op = 'S';

        $MyArc = 4 / 3 * (sqrt(2) - 1);

        $this->_out(sprintf('%.2F %.2F m', ($x + $r) * $k, ($hp - $y) * $k));

        $xc = $x;
        $yc = $y;

        $this->_Arc($xc + $r, $yc, $xc + $r, $yc - $r * $MyArc, $xc + $r * $MyArc, $yc - $r, $xc, $yc - $r);
        $this->_Arc($xc, $yc - $r, $xc - $r * $MyArc, $yc - $r, $xc - $r, $yc - $r * $MyArc, $xc - $r, $yc);
        $this->_Arc($xc - $r, $yc, $xc - $r, $yc + $r * $MyArc, $xc - $r * $MyArc, $yc + $r, $xc, $yc + $r);
        $this->_Arc($xc, $yc + $r, $xc + $r * $MyArc, $yc + $r, $xc + $r, $yc + $r * $MyArc, $xc + $r, $yc);

        $this->_out($op);
    }

    function _Arc($x1, $y1, $x2, $y2, $x3, $y3, $x4, $y4)
    {
        $k = $this->k;
        $hp = $this->h;

        $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c',
            $x1 * $k, ($hp - $y1) * $k,
            $x2 * $k, ($hp - $y2) * $k,
            $x3 * $k, ($hp - $y3) * $k));
        $this->_out(sprintf('%.2F %.2F %.2F %.2F v',
            $x4 * $k, ($hp - $y4) * $k,
            $x4 * $k, ($hp - $y4) * $k));
    }

    function Header()
    {
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, utf8_decode('Reporte de Trimestre #' . $GLOBALS['idTrimestre']), 0, 1, 'C');
        $this->Ln(5);

        // Mostrar tutor, grupo, estudiante
        $this->SetFont('Arial', '', 12);
        $this->Cell(0, 8, utf8_decode("Tutor: " . $GLOBALS['tutorNombre']), 0, 1);
        $this->Cell(0, 8, utf8_decode("Grupo: " . $GLOBALS['grupoNombre']), 0, 1);
        $this->Cell(0, 8, utf8_decode("Estudiante: " . $GLOBALS['estudiante']['apellidos'] . ", " . $GLOBALS['estudiante']['nombres']), 0, 1);
        $this->Ln(10);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo(), 0, 0, 'C');
    }
}

// Crear PDF
$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);



switch ($colorSemaforo) {
    case 'blue':
        $r = 0; $g = 0; $b = 255;
        break;
    case 'green':
        $r = 0; $g = 128; $b = 0;
        break;
    case 'yellow':
        $r = 255; $g = 165; $b = 0;
        break;
    case 'red':
        $r = 255; $g = 0; $b = 0;
        break;
    default:
        $r = 0; $g = 0; $b = 0;
}

// Dibujar círculo relleno (semáforo)
$pdf->SetFillColor($r, $g, $b);
$x = $pdf->GetX() + 160;
$y = $pdf->GetY() -30;
$radio = 8;
$pdf->Circle($x, $y + $radio, $radio, 'F');


// --- Aspectos positivos ---
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, utf8_decode('Aspectos positivos'), 0, 1);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(10, 8, '#', 1);
$pdf->Cell(35, 8, 'Fecha', 1);
$pdf->Cell(140, 8, utf8_decode('Descripción'), 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 11);
$contador = 1;
foreach ($aspectosPositivos as $asp) {
    $fecha = date('d/m/Y', strtotime($asp['fecha']));
    $pdf->Cell(10, 8, $contador++, 1);
    $pdf->Cell(35, 8, $fecha, 1);
    $pdf->Cell(140, 8, utf8_decode($asp['descripcion']), 1);
    $pdf->Ln();
}
$pdf->Ln(5);

// --- Aspectos a mejorar ---
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, utf8_decode('Aspectos a mejorar'), 0, 1);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(10, 8, '#', 1);
$pdf->Cell(35, 8, 'Fecha', 1);
$pdf->Cell(115, 8, utf8_decode('Descripción'), 1);
$pdf->Cell(25, 8, 'Tipo', 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 11);
$contador = 1;
foreach ($aspectosMejorar as $asp) {
    $fecha = date('d/m/Y', strtotime($asp['fecha']));
    $pdf->Cell(10, 8, $contador++, 1);
    $pdf->Cell(35, 8, $fecha, 1);
    $pdf->Cell(115, 8, utf8_decode($asp['descripcion']), 1);
    $pdf->Cell(25, 8, utf8_decode($asp['tipo']), 1);
    $pdf->Ln();
}
$pdf->Ln(5);

// --- Registro de Inasistencia ---
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, utf8_decode('Registro de Inasistencia'), 0, 1);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(10, 8, '#', 1);
$pdf->Cell(50, 8, 'Fecha', 1);
$pdf->Cell(70, 8, 'Tipo', 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 11);
$contador = 1;
foreach ($inasistencias as $ina) {
    $fecha = date('d/m/Y', strtotime($ina['fecha']));
    $tipo = $ina['tipo'] === 'I' ? 'Injustificada' : ($ina['tipo'] === 'J' ? 'Justificada' : 'Desconocida');
    $pdf->Cell(10, 8, $contador++, 1);
    $pdf->Cell(50, 8, $fecha, 1);
    $pdf->Cell(70, 8, utf8_decode($tipo), 1);
    $pdf->Ln();
}



$pdf->Output('I', 'Reporte_Trimestre.pdf');
