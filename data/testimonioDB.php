<?php
// DATA/testimonioDB.php
// Funciones relacionadas con testimonios
/**
 * Se encarga de interactuar con la base de datos con la tabla  hay que crear una clase por cada tabla en este caso solo tenemos una tabla entonces hacemos solo una clase, (clase libro db) para hacerle consultas a la base de datos.
 */
class testimonioDB {

    private $db;
    private $table = 'testimonios';
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
            $testimonio = [];
            //en cada vuelta obtengo un array asociativo con los datos de una fila y lo guardo en la variable $row
            //cuando ya no quedan filas que recorrer termina el bucle
            while($row = $resultado->fetch_assoc()){
                //al array libros le añado $row 
                $testimonio[] = $row;
            }
            //devolvemos el resultado
            return $testimonio;
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
    



    public function guardarTestimonio($id_usuario, $mensaje) {
        $sql = "INSERT INTO testimonios (id_usuario, mensaje, fecha, visible) VALUES (?, ?, CURDATE(), 0)";
        $stmt = $this->db->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("is", $id_usuario, $mensaje);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        }
        return false;
    }

    public function obtenerTestimoniosVisibles() {
        $sql = "
            SELECT t.id, t.mensaje, t.fecha, u.nombre AS nombre_usuario
            FROM testimonios t
            JOIN usuarios u ON t.id_usuario = u.id
            WHERE t.visible = 1
            ORDER BY t.fecha DESC
        ";
        $resultado = $this->db->query($sql);
        $testimonios = [];
        if ($resultado && $resultado->num_rows > 0) {
            while ($row = $resultado->fetch_assoc()) {
                $testimonios[] = $row;
            }
        }
        return $testimonios;
    }

    public function obtenerTodosLosTestimonios() {
        $sql = "
            SELECT t.id, t.mensaje, t.fecha, u.nombre AS nombre_usuario, t.visible
            FROM testimonios t
            JOIN usuarios u ON t.id_usuario = u.id
            ORDER BY t.fecha DESC
        ";
        $resultado = $this->db->query($sql);
        $testimonios = [];
        if ($resultado && $resultado->num_rows > 0) {
            while ($row = $resultado->fetch_assoc()) {
                $testimonios[] = $row;
            }
        }
        return $testimonios;
    }

    public function actualizarVisibilidadTestimonio($id, $visible) {
        $sql = "UPDATE testimonios SET visible = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("ii", $visible, $id);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        }
        return false;
    }

    public function eliminarTestimonio($id) {
        $sql = "DELETE FROM testimonios WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("i", $id);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        }
        return false;
    }
}