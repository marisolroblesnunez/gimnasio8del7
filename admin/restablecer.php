<?php
session_start();

require_once __DIR__ . '/../controllers/usuarioController.php';

$usuarioController = new UsuarioController();

$mensaje = '';
$tipo_mensaje = '';
$token_valido = false;
$token = $_GET['token'] ?? '';

// Verificar si el token existe en la base de datos (sin verificarlo aún)
// Esto es para saber si mostrar el formulario o un mensaje de error inicial
if (!empty($token)) {
    $database = new Database();
    $usuarioDB = new UsuarioDB($database);
    $usuario = $usuarioDB->getByEmail('dummy@example.com'); // Usar un email dummy para buscar por token
    // En un escenario real, buscarías el usuario por el token directamente
    // Aquí, solo verificamos si el token existe en la tabla para mostrar el formulario
    $sql = "SELECT id FROM usuarios WHERE token_recuperacion = ?";
    $stmt = $database->getConexion()->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $token_valido = true;
        }
        $stmt->close();
    }
    $database->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['restablecer_password'])) {
    $token = $_POST['token'] ?? '';
    $nueva_password = $_POST['password'] ?? '';
    $confirmar_password = $_POST['confirm_password'] ?? '';

    if (empty($token)) {
        $mensaje = 'Token de recuperación no proporcionado.';
        $tipo_mensaje = 'error';
    } elseif (empty($nueva_password) || empty($confirmar_password)) {
        $mensaje = 'Ambos campos de contraseña son obligatorios.';
        $tipo_mensaje = 'error';
    } elseif ($nueva_password !== $confirmar_password) {
        $mensaje = 'Las contraseñas no coinciden.';
        $tipo_mensaje = 'error';
    } else {
        $resultado = $usuarioController->restablecerPassword($token, $nueva_password);

        if ($resultado['success']) {
            $mensaje = $resultado['mensaje'];
            $tipo_mensaje = 'success';
        } else {
            $mensaje = $resultado['mensaje'];
            $tipo_mensaje = 'error';
        }
    }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña</title>
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
        .form-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 30px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            background-color: #fff;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }
        .form-group input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1em;
            box-sizing: border-box;
        }
        .form-group button {
            display: block;
            width: 100%;
            padding: 12px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1.1em;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .form-group button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <?php if (!empty($mensaje)): ?>
        <div class="message-container <?php echo $tipo_mensaje; ?>">
            <p><?php echo htmlspecialchars($mensaje); ?></p>
            <?php if ($tipo_mensaje === 'success'): ?>
                <a href="login.php" class="back-button">Ir a Iniciar Sesión</a>
            <?php endif; ?>
        </div>
    <?php elseif ($token_valido): ?>
        <div class="form-container">
            <h1>Restablecer Contraseña</h1>
            <form method="POST" action="restablecer.php">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                <div class="form-group">
                    <label for="password">Nueva Contraseña:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirmar Contraseña:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                <div class="form-group">
                    <button type="submit" name="restablecer_password">Restablecer Contraseña</button>
                </div>
            </form>
        </div>
    <?php else: ?>
        <div class="message-container error">
            <p>Token de recuperación no válido o no proporcionado.</p>
            <a href="login.php" class="back-button">Volver al Login</a>
        </div>
    <?php endif; ?>
</body>
</html>
