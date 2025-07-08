<?php
session_start();

require_once __DIR__ . '/../controllers/usuarioController.php';

$usuarioController = new UsuarioController();

$mensaje = '';
$tipo_mensaje = '';

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $resultado = $usuarioController->verificarCuenta($token);

    if ($resultado['success']) {
        $mensaje = $resultado['mensaje'];
        $tipo_mensaje = 'success';
    } else {
        $mensaje = $resultado['mensaje'];
        $tipo_mensaje = 'error';
    }
} else {
    $mensaje = 'Token de verificación no proporcionado.';
    $tipo_mensaje = 'error';
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Cuenta</title>
    <link rel="stylesheet" href="css/login.css">
    <style>
        .message-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 30px;
            border-radius: 8px;
            text-align: center;
            font-size: 1.2em;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            background-color: #fff;
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
        .back-button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .back-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="message-container <?php echo $tipo_mensaje; ?>">
        <p><?php echo htmlspecialchars($mensaje); ?></p>
        <a href="login.php" class="back-button">Ir a Iniciar Sesión</a>
    </div>
</body>
</html>