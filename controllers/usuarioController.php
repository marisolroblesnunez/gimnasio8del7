<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../data/usuarioDB.php';

class UsuarioController {
    private $usuarioDB;

    public function __construct() {
        $database = new Database();
        $this->usuarioDB = new UsuarioDB($database);
    }

    public function registrarUsuario($email, $password) {
        return $this->usuarioDB->registrarUsuario($email, $password);
    }

    public function iniciarSesion($email, $password) {
        $resultado = $this->usuarioDB->verificarCredenciales($email, $password);
        if ($resultado['success']) {
            // Iniciar sesión y guardar datos del usuario
            session_start();
            $_SESSION['usuario_id'] = $resultado['usuario']['id'];
            $_SESSION['usuario_email'] = $resultado['usuario']['email'];
            // Puedes guardar más datos si los necesitas en la sesión
        }
        return $resultado;
    }

    public function cerrarSesion() {
        session_start();
        session_unset();
        session_destroy();
        // Redirigir al login o a la página principal
        header('Location: /admin/login.php'); // Ajusta la ruta según tu estructura
        exit();
    }

    public function verificarCuenta($token) {
        return $this->usuarioDB->verificarToken($token);
    }

    public function solicitarRecuperacionPassword($email) {
        return $this->usuarioDB->recuperarPassword($email);
    }

    public function restablecerPassword($token, $nueva_password) {
        return $this->usuarioDB->restablecerPassword($token, $nueva_password);
    }

    public function estaLogueado() {
        session_start();
        return isset($_SESSION['usuario_id']);
    }

    public function getUsuarioId() {
        session_start();
        return $_SESSION['usuario_id'] ?? null;
    }
}
