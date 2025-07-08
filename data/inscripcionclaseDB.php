<?php
/**
 * Se encarga de interactuar con la base de datos con la tabla libro hay que crear una clase por cada tabla en este caso solo tenemos una tabla entonces hacemos solo una clase, (clase libro db) para hacerle consultas a la base de datos.
 */
class InscripcionClaseDB {

    private $db;
    private $table = 'inscripciones_clases';
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
            $inscripciones = [];
            //en cada vuelta obtengo un array asociativo con los datos de una fila y lo guardo en la variable $row
            //cuando ya no quedan filas que recorrer termina el bucle
            while($row = $resultado->fetch_assoc()){
                //al array libros le añado $row 
                $inscripciones[] = $row;
            }
            //devolvemos el resultado
            return $inscripciones;
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
    public function insertarInscripcion($id_usuario, $id_clase) {
        $sql = "INSERT INTO inscripciones_clases (id_usuario, id_clase, fecha_inscripcion) VALUES (?, ?, NOW())";
        $stmt = $this->db->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("ii", $id_usuario, $id_clase);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        }
        return false;
    }

    public function estaInscrito($id_usuario, $id_clase) {
        $sql = "SELECT COUNT(*) FROM inscripciones_clases WHERE id_usuario = ? AND id_clase = ?";
        $stmt = $this->db->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("ii", $id_usuario, $id_clase);
            $stmt->execute();
            $stmt->bind_result($count);
            $stmt->fetch();
            $stmt->close();
            return $count > 0;
        }
        return false;
    }

    public function getInscripcionesByUsuario($id_usuario) {
        $sql = "SELECT ic.*, c.nombre AS nombre_clase, c.dia_semana, c.hora
                FROM inscripciones_clases ic
                JOIN clases c ON ic.id_clase = c.id
                WHERE ic.id_usuario = ?
                ORDER BY c.dia_semana, c.hora";
        $stmt = $this->db->prepare($sql);
        $inscripciones = [];
        if ($stmt) {
            $stmt->bind_param("i", $id_usuario);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $inscripciones[] = $row;
            }
            $stmt->close();
        }
        return $inscripciones;
    }

    public function eliminarInscripcion($id_inscripcion) {
        $sql = "DELETE FROM inscripciones_clases WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("i", $id_inscripcion);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        }
        return false;
    }
}