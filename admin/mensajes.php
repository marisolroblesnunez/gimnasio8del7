<?php
session_start();

require_once __DIR__ . '/../controllers/usuarioController.php';
require_once __DIR__ . '/../controllers/contactoController.php';

$usuarioController = new UsuarioController();
$contactoController = new ContactoController();

// Si el usuario no está logueado, redirigir a login.php
if (!$usuarioController->estaLogueado()) {
    header("Location: login.php");
    exit();
}

$mensaje = '';
$tipo_mensaje = '';

// Procesar acciones de administración (eliminar)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'eliminar') {
        $mensaje_id = (int)$_POST['mensaje_id'];
        
        if ($contactoController->eliminarMensaje($mensaje_id)) {
            $mensaje = 'Mensaje eliminado con éxito.';
            $tipo_mensaje = 'success';
        } else {
            $mensaje = 'Error al eliminar el mensaje.';
            $tipo_mensaje = 'error';
        }
    }
}

$mensajes_contacto = $contactoController->obtenerTodosLosMensajes();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Mensajes de Contacto - Admin</title>
    <link rel="stylesheet" href="css/estilos.css">
    <style>
        .container {
            max-width: 900px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }
        .message {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            text-align: center;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .actions button {
            padding: 8px 12px;
            margin-right: 5px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9em;
        }
        .actions .btn-delete {
            background-color: #dc3545;
            color: white;
        }
        .actions .btn-delete:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Gestión de Mensajes de Contacto</h1>

        <?php if (!empty($mensaje)): ?>
            <div class="message <?php echo $tipo_mensaje; ?>">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>

        <?php if (empty($mensajes_contacto)): ?>
            <p>No hay mensajes de contacto para gestionar.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Mensaje</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($mensajes_contacto as $msg): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($msg['id']); ?></td>
                            <td><?php echo htmlspecialchars($msg['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($msg['email']); ?></td>
                            <td><?php echo htmlspecialchars($msg['mensaje']); ?></td>
                            <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($msg['fecha']))); ?></td>
                            <td class="actions">
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="mensaje_id" value="<?php echo $msg['id']; ?>">
                                    <button type="submit" name="action" value="eliminar" class="btn-delete" onclick="return confirm('¿Estás seguro de que quieres eliminar este mensaje?');">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
