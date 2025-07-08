<?php
session_start();

require_once __DIR__ . '/../controllers/usuarioController.php';

$usuarioController = new UsuarioController();

// Si el usuario ya está logueado, redirigir a index.php
if ($usuarioController->estaLogueado()) {
    header("Location: index.php");
    exit();
}

$mensaje = '';
$tipo_mensaje = '';

// Procesar formulario de Login
if (isset($_POST['login'])) {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $resultado = $usuarioController->iniciarSesion($email, $password);

    if ($resultado['success']) {
        $_SESSION['mensaje'] = $resultado['mensaje'];
        header("Location: index.php");
        exit();
    } else {
        $mensaje = $resultado['mensaje'];
        $tipo_mensaje = 'error';
    }
}

// Procesar formulario de Registro
if (isset($_POST['registro'])) {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $resultado = $usuarioController->registrarUsuario($email, $password);

    if ($resultado['success']) {
        $mensaje = $resultado['mensaje'];
        $tipo_mensaje = 'success';
    } else {
        $mensaje = $resultado['mensaje'];
        $tipo_mensaje = 'error';
    }
}

// Procesar formulario de Recuperación de Contraseña
if (isset($_POST['recuperar'])) {
    $email = $_POST['email'] ?? '';

    $resultado = $usuarioController->solicitarRecuperacionPassword($email);

    if ($resultado['success']) {
        $mensaje = $resultado['mensaje'];
        $tipo_mensaje = 'success';
    } else {
        $mensaje = $resultado['mensaje'];
        $tipo_mensaje = 'error';
    }
}

// Mostrar mensajes de sesión si existen
if (isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
    $tipo_mensaje = (strpos($mensaje, 'exito') !== false || strpos($mensaje, 'correcta') !== false || strpos($mensaje, 'Bienvenido') !== false) ? 'success' : 'error';
    unset($_SESSION['mensaje']);
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/login.css">
    <style>
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
    </style>
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <?php if (!empty($mensaje)): ?>
            <div class="message <?php echo $tipo_mensaje; ?>">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>
        <form method="post" action="login.php">
            <input type="email" name="email" required placeholder="Correo electrónico">
            <input type="password" name="password" required placeholder="Contraseña">
            <input type="submit" name="login" value="Iniciar Sesión">
        </form>
        
        <div class="olvido-password">
            <a class="abrir-modal-recuperar">Recuperar contraseña</a>
        </div>
        <div class="crear-cuenta">
            <a class="abrir-modal-registro">Crear cuenta nueva</a>
        </div>
        
        <div id="modalRecuperar" class="modal">
            <div class="modal-contenido">
                <span class="cerrarRecuperar">&times;</span>
                <h2>Recuperar contraseña</h2>
                <form method="POST" action="login.php">
                    <input type="email" name="email" required placeholder="Correo electrónico">
                    <input type="submit" name="recuperar" value="Recuperar Contraseña">
                </form>
            </div>
        </div>

        <div id="modalRegistro" class="modal">
            <div class="modal-contenido">
                <span class="cerrarRegistro">&times;</span>
                <h2>Registro Cuenta nueva</h2>
                <form method="POST" action="login.php">
                    <input type="email" name="email" required placeholder="Correo electrónico">
                    <input type="password" name="password" required placeholder="Contraseña">
                    <input type="submit" name="registro" value="Registrarse">
                </form>
            </div>
        </div>
    </div>
    <script src="js/login.js"></script>
    
</body>
</html>
