<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../data/mensajecontactoDB.php';

class ContactoController {
    private $mensajeContactoDB;

    public function __construct() {
        $database = new Database();
        $this->mensajeContactoDB = new mensajecontactoDB($database);
    }

    public function procesarFormularioContacto() {
        $errores = [];
        $mensaje_exito = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $mensaje = trim($_POST['mensaje'] ?? '');

            // Validaciones básicas
            if (empty($nombre)) {
                $errores[] = 'El nombre es obligatorio.';
            }
            if (empty($email)) {
                $errores[] = 'El email es obligatorio.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errores[] = 'El formato del email no es válido.';
            }
            if (empty($mensaje)) {
                $errores[] = 'El mensaje es obligatorio.';
            }

            if (empty($errores)) {
                if ($this->mensajeContactoDB->insertarMensContacto($nombre, $email, $mensaje)) {
                    $mensaje_exito = '¡Tu mensaje ha sido enviado con éxito!';
                    // Limpiar campos después de un envío exitoso
                    $_POST['nombre'] = '';
                    $_POST['email'] = '';
                    $_POST['mensaje'] = '';
                } else {
                    $errores[] = 'Hubo un error al enviar tu mensaje. Por favor, inténtalo de nuevo.';
                }
            }
        }
        return ['errores' => $errores, 'mensaje_exito' => $mensaje_exito];
    }

    public function obtenerTodosLosMensajes() {
        return $this->mensajeContactoDB->obtenerMensContacto();
    }

    public function eliminarMensaje($id) {
        return $this->mensajeContactoDB->eliminarMensContacto($id);
    }
}
