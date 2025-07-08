<?php
require_once __DIR__ . '/controllers/testimonioController.php';

$testimonioController = new TestimonioController();
$resultado_envio = $testimonioController->procesarEnvioTestimonio();
$errores_envio = $resultado_envio['errores'];
$mensaje_exito_envio = $resultado_envio['mensaje_exito'];

$testimonios_visibles = $testimonioController->obtenerTestimoniosParaWeb();

// Para mantener el valor en el formulario después de un error
$mensaje_val = $_POST['mensaje'] ?? '';

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Testimonios - Gimnasio</title>
    <link rel="stylesheet" href="css/estilos.css">
    <style>
        .testimonios-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .testimonio-card {
            background-color: #fff;
            border: 1px solid #eee;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        .testimonio-card p {
            font-style: italic;
            color: #555;
            margin-bottom: 10px;
        }
        .testimonio-card .autor {
            font-weight: bold;
            text-align: right;
            color: #333;
        }
        .testimonio-card .fecha {
            font-size: 0.8em;
            color: #888;
            text-align: right;
        }
        .form-testimonio {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid #eee;
        }
        .form-testimonio h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            min-height: 100px;
            resize: vertical;
        }
        .form-group button {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
        }
        .form-group button:hover {
            background-color: #218838;
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
        .success-message {
            color: #28a745;
            margin-top: 15px;
            text-align: center;
            font-size: 1.1em;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="testimonios-container">
        <h1>Lo que dicen nuestros clientes</h1>

        <?php if (empty($testimonios_visibles)): ?>
            <p>Aún no hay testimonios publicados. ¡Sé el primero en dejar uno!</p>
        <?php else: ?>
            <?php foreach ($testimonios_visibles as $testimonio): ?>
                <div class="testimonio-card">
                    <p>"<?php echo htmlspecialchars($testimonio['mensaje']); ?>"</p>
                    <div class="autor">- <?php echo htmlspecialchars($testimonio['nombre_usuario'] ?? 'Anónimo'); ?></div>
                    <div class="fecha"><?php echo htmlspecialchars(date('d/m/Y', strtotime($testimonio['fecha']))); ?></div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <div class="form-testimonio">
            <h2>Deja tu Testimonio</h2>

            <?php if (!empty($errores_envio)): ?>
                <ul class="error-list">
                    <?php foreach ($errores_envio as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <?php if (!empty($mensaje_exito_envio)): ?>
                <p class="success-message"><?php echo htmlspecialchars($mensaje_exito_envio); ?></p>
            <?php endif; ?>

            <form action="testimonios.php" method="POST">
                <div class="form-group">
                    <label for="mensaje">Tu Mensaje:</label>
                    <textarea id="mensaje" name="mensaje" required><?php echo htmlspecialchars($mensaje_val); ?></textarea>
                </div>
                <div class="form-group">
                    <button type="submit">Enviar Testimonio</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
