<?php
require_once __DIR__ . '/controllers/contactoController.php';

$contactoController = new ContactoController();
$resultado = $contactoController->procesarFormularioContacto();
$errores = $resultado['errores'];
$mensaje_exito = $resultado['mensaje_exito'];

// Para mantener los valores en el formulario después de un error
$nombre_val = $_POST['nombre'] ?? '';
$email_val = $_POST['email'] ?? '';
$mensaje_val = $_POST['mensaje'] ?? '';

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacto - Gimnasio</title>
    <link rel="stylesheet" href="css/estilos.css">
    <style>
        .form-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            background-color: #fff;
        }
        .form-container h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
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
        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1em;
            box-sizing: border-box; /* Para incluir padding y border en el ancho */
        }
        .form-group textarea {
            resize: vertical;
            min-height: 120px;
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
        .error-message {
            color: #dc3545;
            margin-top: 5px;
            font-size: 0.9em;
        }
        .success-message {
            color: #28a745;
            margin-top: 15px;
            text-align: center;
            font-size: 1.1em;
            font-weight: bold;
        }
        .error-list {
            list-style-type: none;
            padding: 0;
            margin-bottom: 20px;
        }
        .error-list li {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Contáctanos</h1>

        <?php if (!empty($errores)): ?>
            <ul class="error-list">
                <?php foreach ($errores as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php if (!empty($mensaje_exito)): ?>
            <p class="success-message"><?php echo htmlspecialchars($mensaje_exito); ?></p>
        <?php endif; ?>

        <form action="contacto.php" method="POST">
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($nombre_val); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email_val); ?>" required>
            </div>
            <div class="form-group">
                <label for="mensaje">Mensaje:</label>
                <textarea id="mensaje" name="mensaje" required><?php echo htmlspecialchars($mensaje_val); ?></textarea>
            </div>
            <div class="form-group">
                <button type="submit">Enviar Mensaje</button>
            </div>
        </form>
    </div>
</body>
</html>
