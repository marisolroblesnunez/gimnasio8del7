<?php
/**
 * Se encarga de interactuar con la base de datos con la tabla libro hay que crear una clase por cada tabla en este caso solo tenemos una tabla entonces hacemos solo una clase, (clase libro db) para hacerle consultas a la base de datos.
 */
class entrenadorDB {

    private $db;
    private $table = 'entrenadores';
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
            $entrenador = [];
            //en cada vuelta obtengo un array asociativo con los datos de una fila y lo guardo en la variable $row
            //cuando ya no quedan filas que recorrer termina el bucle
            while($row = $resultado->fetch_assoc()){
                //al array libros le añado $row 
                $entrenador[] = $row;
            }
            //devolvemos el resultado
            return $entrenador;
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
    //agregar más métodos según sea necesario, como insertar, actualizar o eliminar entrenadores.
    public function insert($nombre, $especialidad, $telefono, $email) {
        $sql = "INSERT INTO {$this->table} (nombre, especialidad, telefono, email) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("ssss", $nombre, $especialidad, $telefono, $email);
            if ($stmt->execute()) {
                return true; // Inserción exitosa
            }
            $stmt->close();
        }
        return false; // Fallo en la inserción
    }
    public function update($id, $nombre, $especialidad, $telefono, $email) {
        $sql = "UPDATE {$this->table} SET nombre = ?, especialidad = ?, telefono = ?, email = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("ssssi", $nombre, $especialidad, $telefono, $email, $id);
            if ($stmt->execute()) {
                return true; // Actualización exitosa
            }
            $stmt->close();
        }
        return false; // Fallo en la actualización
    }
    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                return true; // Eliminación exitosa
            }
            $stmt->close();
        }
        return false; // Fallo en la eliminación
    }
    public function getByEmail($email) {
        $sql = "SELECT * FROM {$this->table} WHERE email = ?";
        $stmt = $this->db->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                return $result->fetch_assoc(); // Devuelve el primer entrenador encontrado
            }
            $stmt->close();
        }
        return null; // No se encontró ningún entrenador con ese email
    }
    public function getByEspecialidad($especialidad) {
        $sql = "SELECT * FROM {$this->table} WHERE especialidad = ?";
        $stmt = $this->db->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("s", $especialidad);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                return $result->fetch_all(MYSQLI_ASSOC); // Devuelve todos los entrenadores con esa especialidad
            }
            $stmt->close();
        }
        return []; // No se encontraron entrenadores con esa especialidad
    }
    public function getAllWithPagination($offset, $limit) {
        $sql = "SELECT * FROM {$this->table} LIMIT ?, ?";
        $stmt = $this->db->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("ii", $offset, $limit);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                return $result->fetch_all(MYSQLI_ASSOC); // Devuelve todos los entrenadores con esa especialidad
            }
            $stmt->close();
        }
        return []; // No se encontraron entrenadores con esa especialidad
    }
    public function countAll() {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $result = $this->db->query($sql);
        if ($result) {
            $row = $result->fetch_assoc();
            return $row['total']; // Devuelve el número total de entrenadores
        }
        return 0; // En caso de error, devuelve 0
    }
    public function search($keyword) {
        $sql = "SELECT * FROM {$this->table} WHERE nombre LIKE ? OR especialidad LIKE ?";
        $stmt = $this->db->prepare($sql);
        if ($stmt) {
            $likeKeyword = "%{$keyword}%";
            $stmt->bind_param("ss", $likeKeyword, $likeKeyword);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                return $result->fetch_all(MYSQLI_ASSOC); // Devuelve todos los entrenadores que coinciden con la búsqueda
            }
            $stmt->close();
        }
        return []; // No se encontraron entrenadores que coincidan con la búsqueda
    }
    public function getSpecialties() {
        $sql = "SELECT DISTINCT especialidad FROM {$this->table}";
        $result = $this->db->query($sql);
        if ($result) {
            return $result->fetch_all(MYSQLI_ASSOC); // Devuelve todas las especialidades únicas
        }
        return []; // En caso de error, devuelve un array vacío
    }
    public function getByPhone($telefono) {
        $sql = "SELECT * FROM {$this->table} WHERE telefono = ?";
        $stmt = $this->db->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("s", $telefono);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                return $result->fetch_assoc(); // Devuelve el primer entrenador encontrado
            }
            $stmt->close();
        }
        return null; // No se encontró ningún entrenador con ese teléfono
    }
    public function getByName($nombre) {
        $sql = "SELECT * FROM {$this->table} WHERE nombre = ?";
        $stmt = $this->db->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("s", $nombre);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                return $result->fetch_assoc(); // Devuelve el primer entrenador encontrado
            }
            $stmt->close();
        }
        return null; // No se encontró ningún entrenador con ese nombre
    }
    public function getBySpecialtyAndName($especialidad, $nombre) {
        $sql = "SELECT * FROM {$this->table} WHERE especialidad = ? AND nombre = ?";
        $stmt = $this->db->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("ss", $especialidad, $nombre);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                return $result->fetch_assoc(); // Devuelve el primer entrenador encontrado
            }
            $stmt->close();
        }
        return null; // No se encontró ningún entrenador con esa especialidad y nombre
    }
    public function getBySpecialtyAndPhone($especialidad, $telefono) {
        $sql = "SELECT * FROM {$this->table} WHERE especialidad = ? AND telefono = ?";
        $stmt = $this->db->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("ss", $especialidad, $telefono);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                return $result->fetch_assoc(); // Devuelve el primer entrenador encontrado
            }
            $stmt->close();
        }
        return null; // No se encontró ningún entrenador con esa especialidad y teléfono
    }
    public function getBySpecialtyAndEmail($especialidad, $email) {
        $sql = "SELECT * FROM {$this->table} WHERE especialidad = ? AND email = ?";
        $stmt = $this->db->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("ss", $especialidad, $email);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                return $result->fetch_assoc(); // Devuelve el primer entrenador encontrado
            }
            $stmt->close();
        }
        return null; // No se encontró ningún entrenador con esa especialidad y email
    }
    public function getBySpecialtyAndNameAndPhone($especialidad, $nombre, $telefono) {
        $sql = "SELECT * FROM {$this->table} WHERE especialidad = ? AND nombre = ? AND telefono = ?";
        $stmt = $this->db->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("sss", $especialidad, $nombre, $telefono);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                return $result->fetch_assoc(); // Devuelve el primer entrenador encontrado
            }
            $stmt->close();
        }
        return null; // No se encontró ningún entrenador con esa especialidad, nombre y teléfono
    }
    public function getBySpecialtyAndNameAndEmail($especialidad, $nombre, $email) {
        $sql = "SELECT * FROM {$this->table} WHERE especialidad = ? AND nombre = ? AND email = ?";
        $stmt = $this->db->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("sss", $especialidad, $nombre, $email);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                return $result->fetch_assoc(); // Devuelve el primer entrenador encontrado
            }
            $stmt->close();
        }
        return null; // No se encontró ningún entrenador con esa especialidad, nombre y email
    }
    public function getBySpecialtyAndPhoneAndEmail($especialidad, $telefono, $email) {
        $sql = "SELECT * FROM {$this->table} WHERE especialidad = ? AND telefono = ? AND email = ?";
        $stmt = $this->db->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("sss", $especialidad, $telefono, $email);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                return $result->fetch_assoc(); // Devuelve el primer entrenador encontrado
            }
            $stmt->close();
        }
        return null; // No se encontró ningún entrenador con esa especialidad, teléfono y email
    }
    public function getBySpecialtyAndNameAndPhoneAndEmail($especialidad, $nombre, $telefono, $email) {
        $sql = "SELECT * FROM {$this->table} WHERE especialidad = ? AND nombre = ? AND telefono = ? AND email = ?";
        $stmt = $this->db->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("ssss", $especialidad, $nombre, $telefono, $email);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                return $result->fetch_assoc(); // Devuelve el primer entrenador encontrado
            }
            $stmt->close();
        }
        return null; // No se encontró ningún entrenador con esa especialidad, nombre, teléfono y email
    }
    
}