<?php
session_start();

require_once __DIR__ . '/../controllers/usuarioController.php';
require_once __DIR__ . '/../controllers/testimonioController.php';

$usuarioController = new UsuarioController();
$testimonioController = new TestimonioController();

// Si el usuario no está logueado, redirigir a login.php
if (!$usuarioController->estaLogueado()) {
    header("Location: login.php");
    exit();
}

$mensaje = '';
$tipo_mensaje = '';

// Procesar acciones de administración (aprobar/rechazar/eliminar)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $testimonio_id = (int)$_POST['testimonio_id'];
        
        switch ($_POST['action']) {
            case 'aprobar':
                if ($testimonioController->actualizarVisibilidad($testimonio_id, 1)) {
                    $mensaje = 'Testimonio aprobado con éxito.';
                    $tipo_mensaje = 'success';
                } else {
                    $mensaje = 'Error al aprobar el testimonio.';
                    $tipo_mensaje = 'error';
                }
                break;
            case 'rechazar':
                if ($testimonioController->actualizarVisibilidad($testimonio_id, 0)) {
                    $mensaje = 'Testimonio rechazado con éxito.';
                    $tipo_mensaje = 'success';
                } else {
                    $mensaje = 'Error al rechazar el testimonio.';
                    $tipo_mensaje = 'error';
                }
                break;
            case 'eliminar':
                if ($testimonioController->eliminarTestimonio($testimonio_id)) {
                    $mensaje = 'Testimonio eliminado con éxito.';
                    $tipo_mensaje = 'success';
                } else {
                    $mensaje = 'Error al eliminar el testimonio.';
                    $tipo_mensaje = 'error';
                }
                break;
        }
    }
}

$testimonios = $testimonioController->obtenerTodosLosTestimoniosAdmin();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Testimonios - Admin</title>
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
        .actions .btn-approve {
            background-color: #28a745;
            color: white;
        }
        .actions .btn-approve:hover {
            background-color: #218838;
        }
        .actions .btn-reject {
            background-color: #ffc107;
            color: #333;
        }
        .actions .btn-reject:hover {
            background-color: #e0a800;
        }
        .actions .btn-delete {
            background-color: #dc3545;
            color: white;
        }
        .actions .btn-delete:hover {
            background-color: #c82333;
        }
        .status-visible {
            color: #28a745;
            font-weight: bold;
        }
        .status-pending {
            color: #ffc107;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Gestión de Testimonios</h1>

        <?php if (!empty($mensaje)): ?>
            <div class="message <?php echo $tipo_mensaje; ?>">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>

        <?php if (empty($testimonios)): ?>
            <p>No hay testimonios para gestionar.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Mensaje</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($testimonios as $testimonio): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($testimonio['id']); ?></td>
                            <td><?php echo htmlspecialchars($testimonio['nombre_usuario'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($testimonio['mensaje']); ?></td>
                            <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($testimonio['fecha']))); ?></td>
                            <td>
                                <?php if ($testimonio['visible'] == 1): ?>
                                    <span class="status-visible">Aprobado</span>
                                <?php else: ?>
                                    <span class="status-pending">Pendiente</span>
                                <?php endif; ?>
                            </td>
                            <td class="actions">
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="testimonio_id" value="<?php echo $testimonio['id']; ?>">
                                    <?php if ($testimonio['visible'] == 0): ?>
                                        <button type="submit" name="action" value="aprobar" class="btn-approve">Aprobar</button>
                                    <?php else: ?>
                                        <button type="submit" name="action" value="rechazar" class="btn-reject">Rechazar</button>
                                    <?php endif; ?>
                                    <button type="submit" name="action" value="eliminar" class="btn-delete" onclick="return confirm('¿Estás seguro de que quieres eliminar este testimonio?');">Eliminar</button>
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
