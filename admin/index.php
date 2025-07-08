<?php
session_start();

require_once __DIR__ . '/../controllers/usuarioController.php';

$usuarioController = new UsuarioController();

// Si el usuario no está logueado, redirigir a login.php
if (!$usuarioController->estaLogueado()) {
    header("Location: login.php");
    exit();
}

$mensaje = '';
if (isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
    unset($_SESSION['mensaje']);
}

// Lógica para cerrar sesión
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    $usuarioController->cerrarSesion();
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Gimnasio</title>
    <link rel="stylesheet" href="css/estilos.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .header {
            background-color: #333;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            margin: 0;
            font-size: 1.8em;
        }
        .header .logout-button {
            background-color: #dc3545;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }
        .header .logout-button:hover {
            background-color: #c82333;
        }
        .container {
            padding: 20px;
            max-width: 1200px;
            margin: 20px auto;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
        .admin-sections {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        .admin-section-card {
            background-color: #e9ecef;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .admin-section-card h3 {
            margin-top: 0;
            color: #333;
        }
        .admin-section-card a {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .admin-section-card a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Panel de Administración</h1>
        <a href="?action=logout" class="logout-button">Cerrar Sesión</a>
    </div>

    <div class="container">
        <?php if (!empty($mensaje)): ?>
            <div class="message success">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>

        <p>Bienvenido al panel de administración del gimnasio.</p>

        <div class="admin-sections">
            <div class="admin-section-card">
                <h3>Gestión de Clases</h3>
                <p>Administra las clases, horarios y cupos.</p>
                <a href="#">Ir a Clases</a>
            </div>
            <div class="admin-section-card">
                <h3>Gestión de Usuarios</h3>
                <p>Administra los usuarios registrados.</p>
                <a href="#">Ir a Usuarios</a>
            </div>
            <div class="admin-section-card">
                <h3>Gestión de Testimonios</h3>
                <p>Revisa y aprueba los testimonios de los clientes.</p>
                <a href="#">Ir a Testimonios</a>
            </div>
            <div class="admin-section-card">
                <h3>Mensajes de Contacto</h3>
                <p>Revisa los mensajes enviados a través del formulario de contacto.</p>
                <a href="#">Ir a Mensajes</a>
            </div>
        </div>
    </div>
</body>
</html>
