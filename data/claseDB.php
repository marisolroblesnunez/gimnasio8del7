<?php
// DATA/claseDB.php
// Funciones para consultar la base de datos relacionadas con clases
/**
 * Se encarga de interactuar con la base de datos con la tabla libro hay que crear una clase por cada tabla en este caso solo tenemos una tabla entonces hacemos solo una clase, (clase libro db) para hacerle consultas a la base de datos.
 */
class claseDB {

    private $db;
    private $table = 'clases';
    //recibe una conexión ($database) a una base de datos y la mete en $db
    public function __construct($database){
        $this->db = $database->getConexion();
    }

    //extrae todos los datos de la tabla $table
    public  function getAll(){
        //construye la consulta
        $sql = "SELECT * FROM {$this->table}";

        //realiza la consulta con la función query()
        $resultado = $this->db->query($sql);

        //comprueba si hay respuesta ($resultado) y si la respuesta viene con datos
        if($resultado && $resultado->num_rows > 0){
            //crea un array para guardar los datos
            $clases = [];
            //en cada vuelta obtengo un array asociativo con los datos de una fila y lo guardo en la variable $row
            //cuando ya no quedan filas que recorrer termina el bucle
            while($row = $resultado->fetch_assoc()){
                //al array libros le añado $row 
                $clases[] = $row;
            }
            //devolvemos el resultado
            return $clases;
        }else{
            //no hay datos, devolvemos un array vacío
            return [];
        }
        
    }

    public function getById($id){
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        if($stmt){
            //añado un parámetro a la consulta
            //este va en el lugar de la ? en la variable $sql
            //"i" es para asegurarnos de que el parámetro es un número entero
            $stmt->bind_param("i", $id);
            //ejecuta la consulta
            $stmt->execute();
            //lee el resultado de la consulta
            $result = $stmt->get_result();

            //comprueba si en el resultado hay datos o está vacío
            if($result->num_rows > 0){
                //devuelve un array asociativo con los datos
                return $result->fetch_assoc();
            }
            //cierra 
            $stmt->close();
        }
        //algo falló
        return null;
    }


    
   /////////////////ESTO NO SE SI PONERLO AQUI?????
    // Devuelve un array asociativo con los datos de la clase o null si no se encuentra
    // Parámetros:
    // - $nombre: el nombre de la clase a buscar
    // Retorna:
    // - Un array asociativo con los datos de la clase si se encuentra, o null si no se encuentra
    // Ejemplo de uso:
    // $database = new Database();
    // $ClaseDB = new claseDB($database);
    //  $clase = $claseDB->getByName('Yoga');
    // if ($clase) {
    //  echo "Clase encontrada: " . $clase['nombre'];
    //  } else {
    //      echo "Clase no encontrada.";
    // }


 // Obtiene una clase por su nombre

    function getByName($nombre) {
        $sql = "SELECT * FROM {$this->table} WHERE nombre = ?";
        $stmt = $this->db->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("s", $nombre);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                return $result->fetch_assoc();
            }
            $stmt->close();
        }
        return null;
    }

    public function insertarClase($nombre, $descripcion, $dia_semana, $hora, $duracion_minutos, $cupo_maximo, $id_entrenador) {
        $sql = "INSERT INTO clases (nombre, descripcion, dia_semana, hora, duracion_minutos, cupo_maximo, id_entrenador) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("sssssii", $nombre, $descripcion, $dia_semana, $hora, $duracion_minutos, $cupo_maximo, $id_entrenador);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        }
        return false;
    }

    public function actualizarClase($id, $nombre, $descripcion, $dia_semana, $hora, $duracion_minutos, $cupo_maximo, $id_entrenador) {
        $sql = "UPDATE clases SET nombre = ?, descripcion = ?, dia_semana = ?, hora = ?, duracion_minutos = ?, cupo_maximo = ?, id_entrenador = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("sssssiii", $nombre, $descripcion, $dia_semana, $hora, $duracion_minutos, $cupo_maximo, $id_entrenador, $id);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        }
        return false;
    }

    public function eliminarClase($id) {
        $sql = "DELETE FROM clases WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("i", $id);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        }
        return false;
    }

    public function getAllClasesWithDetails() {
        $sql = "SELECT c.*, e.nombre AS nombre_entrenador, COUNT(ic.id_clase) AS inscritos_actuales
                FROM clases c
                LEFT JOIN entrenadores e ON c.id_entrenador = e.id
                LEFT JOIN inscripciones_clases ic ON c.id = ic.id_clase
                GROUP BY c.id
                ORDER BY c.dia_semana, c.hora";
        $resultado = $this->db->query($sql);
        $clases = [];
        if ($resultado && $resultado->num_rows > 0) {
            while ($row = $resultado->fetch_assoc()) {
                $clases[] = $row;
            }
        }
        return $clases;
    }

    public function getClasesByDia($dia_semana) {
        $sql = "SELECT c.*, e.nombre AS nombre_entrenador, COUNT(ic.id_clase) AS inscritos_actuales
                FROM clases c
                LEFT JOIN entrenadores e ON c.id_entrenador = e.id
                LEFT JOIN inscripciones_clases ic ON c.id = ic.id_clase
                WHERE c.dia_semana = ?
                GROUP BY c.id
                ORDER BY c.hora";
        $stmt = $this->db->prepare($sql);
        $clases = [];
        if ($stmt) {
            $stmt->bind_param("s", $dia_semana);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $clases[] = $row;
            }
            $stmt->close();
        }
        return $clases;
    }

    public function getInscritosCount($id_clase) {
        $sql = "SELECT COUNT(*) AS count FROM inscripciones_clases WHERE id_clase = ?";
        $stmt = $this->db->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("i", $id_clase);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();
            return $row['count'];
        }
        return 0;
    }

}