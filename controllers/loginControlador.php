<?php
session_start();
require_once __DIR__ . '/../models/usuarioModelo.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'] ?? '';
    $clave = $_POST['clave'] ?? '';

    $datos = validarUsuario($usuario, $clave);

    if ($datos) {
        $_SESSION['usuario'] = $datos['usuario'];
        $_SESSION['rol'] = strtolower($datos['rol']);  // Aseguramos minúsculas

        if ($_SESSION['rol'] === 'tutor') {
            if (!empty($datos['codigo_tutor'])) {
                $_SESSION['codigo_tutor'] = $datos['codigo_tutor'];
            } else {
                // Si no tiene código tutor, impedir acceso
                header("Location: /DesafioPractico_Academia/app/views/login.php?error=2");
                exit();
            }
            header("Location: /DesafioPractico_Academia/app/views/dashboard_tutor.php");
            exit();
        } elseif ($_SESSION['rol'] === 'admin') {
            $_SESSION['codigo_tutor'] = null;
            header("Location: /DesafioPractico_Academia/app/views/dashboard_admin.php");
            exit();
        } else {
            header("Location: /DesafioPractico_Academia/app/views/login.php?error=3");
            exit();
        }
    } else {
        header("Location: /DesafioPractico_Academia/app/views/login.php?error=1");
        exit();
    }
} else {
    header("Location: /DesafioPractico_Academia/app/views/login.php");
    exit();
}
?>
