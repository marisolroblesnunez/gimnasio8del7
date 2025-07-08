<?php
require_once __DIR__ . '/controllers/inscripcionController.php';

$inscripcionController = new InscripcionController();
$resultado = $inscripcionController->procesarInscripcion();

$mensaje = $resultado['message'];
$tipo_mensaje = $resultado['success'] ? 'success' : 'error';

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado de Inscripción</title>
    <link rel="stylesheet" href="css/estilos.css">
    <style>
        .message-container {
            margin-top: 50px;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            font-size: 1.2em;
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
        <a href="clases.php" class="back-button">Volver a Clases</a>
    </div>
</body>
</html>
