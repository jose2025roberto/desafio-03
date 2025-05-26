<?php
require_once __DIR__ . '../../config/conexion.php';

function validarUsuario($usuario, $clave) {
    global $conn;
    $sql = "SELECT usuario, password, rol, codigo_tutor FROM usuarios WHERE usuario = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        // Manejo básico de error de preparación de consulta
        return false;
    }
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado && $resultado->num_rows === 1) {
        $fila = $resultado->fetch_assoc();
        $hashInput = hash('sha256', $clave);
        if (hash_equals($fila['password'], $hashInput)) {
            return [
                'usuario' => $fila['usuario'],
                'rol' => $fila['rol'],
                'codigo_tutor' => $fila['codigo_tutor'] ?? null
            ];
        }
    }
    return false;
}
?>


