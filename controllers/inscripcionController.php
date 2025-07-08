<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../data/inscripcionclaseDB.php';
require_once __DIR__ . '/../data/claseDB.php';

class InscripcionController {
    private $inscripcionClaseDB;
    private $claseDB;

    public function __construct() {
        $database = new Database();
        $this->inscripcionClaseDB = new InscripcionClaseDB($database);
        $this->claseDB = new ClaseDB($database);
    }

    public function procesarInscripcion() {
        // Asegúrate de que el usuario esté logueado y tengas su ID
        // Por ahora, asumiremos un ID de usuario de ejemplo o que viene de la sesión.
        // Obtener el id_usuario de la sesión
        session_start();
        $id_usuario = $_SESSION['usuario_id'] ?? null;

        if (!$id_usuario) {
            return ['success' => false, 'message' => 'Debes iniciar sesión para inscribirte a una clase.'];
        }

        if (isset($_GET['clase_id'])) {
            $id_clase = (int)$_GET['clase_id'];

            // 1. Verificar si la clase existe y obtener su cupo máximo
            $clase = $this->claseDB->getById($id_clase);
            if (!$clase) {
                return ['success' => false, 'message' => 'Clase no encontrada.'];
            }

            // 2. Verificar si el usuario ya está inscrito en esta clase
            if ($this->inscripcionClaseDB->estaInscrito($id_usuario, $id_clase)) {
                return ['success' => false, 'message' => 'Ya estás inscrito en esta clase.'];
            }

            // 3. Verificar el cupo máximo
            $inscritos_actuales = $this->claseDB->getInscritosCount($id_clase);
            if ($inscritos_actuales >= $clase['cupo_maximo']) {
                return ['success' => false, 'message' => 'El cupo para esta clase está lleno.'];
            }

            // 4. Realizar la inscripción
            if ($this->inscripcionClaseDB->insertarInscripcion($id_usuario, $id_clase)) {
                return ['success' => true, 'message' => 'Inscripción exitosa!'];
            } else {
                return ['success' => false, 'message' => 'Error al procesar la inscripción.'];
            }
        }
        return ['success' => false, 'message' => 'ID de clase no proporcionado.'];
    }

    public function mostrarInscripcionesUsuario($id_usuario) {
        return $this->inscripcionClaseDB->getInscripcionesByUsuario($id_usuario);
    }

    public function eliminarInscripcionUsuario($id_inscripcion) {
        return $this->inscripcionClaseDB->eliminarInscripcion($id_inscripcion);
    }
}
