<?php
// DATA/contactoDB.php
// Función para insertar mensajes en la tabla mensajes_contacto

/**
 * Se encarga de interactuar con la base de datos con la tabla libro hay que crear una clase por cada tabla en este caso solo tenemos una tabla entonces hacemos solo una clase, (clase libro db) para hacerle consultas a la base de datos.
 */
class mensajecontactoDB {

    private $db;
    private $table = 'mensajes_contacto';
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
            $mensajes = [];
            //en cada vuelta obtengo un array asociativo con los datos de una fila y lo guardo en la variable $row
            //cuando ya no quedan filas que recorrer termina el bucle
            while($row = $resultado->fetch_assoc()){
                //al array libros le añado $row 
                $mensajes[] = $row;
            }
            //devolvemos el resultado
            return $mensajes;
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
    




// public function guardarMensajeContacto($pdo, $nombre, $email, $mensaje) {
//     $sql = "INSERT INTO mensajes_contacto (nombre, email, mensaje, fecha) VALUES (?, ?, ?, CURDATE())";
//     $stmt = $pdo->prepare($sql);
//     return $stmt->execute([$nombre, $email, $mensaje]);
// }
// public function obtenerMensajesContacto($pdo) {
//     $sql = "SELECT nombre, email, mensaje, fecha FROM mensajes_contacto ORDER BY fecha DESC";
//     $stmt = $pdo->query($sql);
//     return $stmt->fetchAll(PDO::FETCH_ASSOC);
// }
// public function eliminarMensajeContacto($pdo, $id) {
//     $sql = "DELETE FROM mensajes_contacto WHERE id = ?";
//     $stmt = $pdo->prepare($sql);
//     return $stmt->execute([$id]);


// }
// public function actualizarMensajeContacto($pdo, $id, $nombre, $email, $mensaje) {
//     $sql = "UPDATE mensajes_contacto SET nombre = ?, email = ?, mensaje = ? WHERE id = ?";
//     $stmt = $pdo->prepare($sql);
//     return $stmt->execute([$nombre, $email, $mensaje, $id]);
// }
 /**
 * Inserta un nuevo mensaje de contacto en la tabla mensajes_contacto
 * @param string $nombre Nombre del remitente
 * @param string $email Email del remitente
 * @param string $mensaje Mensaje del remitente
 * @return bool True si se insertó correctamente, false en caso contrario
 */
public function insertarMensContacto($nombre, $email, $mensaje) {
    $sql = "INSERT INTO mensajes_contacto (nombre, email, mensaje, fecha) VALUES (?, ?, ?, CURDATE())";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([$nombre, $email, $mensaje]);
}
/**
 * Obtiene todos los mensajes de contacto
 * @return array Array de mensajes de contacto
 */
public function obtenerMensContacto() {
    $sql = "SELECT * FROM mensajes_contacto ORDER BY fecha DESC";
    $stmt = $this->db->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
/**
 * Elimina un mensaje de contacto por su ID
 * @param int $id ID del mensaje a eliminar
 * @return bool True si se eliminó correctamente, false en caso contrario
 */
public function eliminarMensContacto($id) {
    $sql = "DELETE FROM mensajes_contacto WHERE id = ?";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([$id]);
}
/**
 * Actualiza un mensaje de contacto por su ID
 * @param int $id ID del mensaje a actualizar
 * @param string $nombre Nuevo nombre del remitente
 * @param string $email Nuevo email del remitente
 * @param string $mensaje Nuevo mensaje del remitente
 * @return bool True si se actualizó correctamente, false en caso contrario
 */
public function actualizarMensContacto($id, $nombre, $email, $mensaje) {
    $sql = "UPDATE mensajes_contacto SET nombre = ?, email = ?, mensaje = ? WHERE id = ?";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([$nombre, $email, $mensaje, $id]);}
}
