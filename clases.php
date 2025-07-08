<?php
require_once __DIR__ . '/controllers/claseController.php';

$claseController = new ClaseController();
$data = $claseController->mostrarClases();
$clases = $data['clases'];
$dia_seleccionado = $data['dia_seleccionado'];

$dias_semana = [
    'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clases del Gimnasio</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <h1>Nuestras Clases</h1>

    <div class="filtros">
        <label for="filtroDia">Filtrar por Día:</label>
        <select id="filtroDia" onchange="location = this.value;">
            <option value="clases.php">Todas las Clases</option>
            <?php foreach ($dias_semana as $dia): ?>
                <option value="clases.php?dia=<?php echo urlencode($dia); ?>" <?php echo ($dia_seleccionado == $dia) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($dia); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="lista-clases">
        <?php if (empty($clases)): ?>
            <p>No hay clases disponibles para el día seleccionado o en este momento.</p>
        <?php else: ?>
            <?php foreach ($clases as $clase): ?>
                <div class="clase-card">
                    <h2><?php echo htmlspecialchars($clase['nombre']); ?></h2>
                    <p><strong>Descripción:</strong> <?php echo htmlspecialchars($clase['descripcion']); ?></p>
                    <p><strong>Día:</strong> <?php echo htmlspecialchars($clase['dia_semana']); ?></p>
                    <p><strong>Hora:</strong> <?php echo htmlspecialchars(substr($clase['hora'], 0, 5)); ?> (Duración: <?php echo htmlspecialchars($clase['duracion_minutos']); ?> min)</p>
                    <p><strong>Entrenador:</strong> <?php echo htmlspecialchars($clase['nombre_entrenador'] ?? 'N/A'); ?></p>
                    <p><strong>Cupo:</strong> <?php echo htmlspecialchars($clase['inscritos_actuales']); ?> / <?php echo htmlspecialchars($clase['cupo_maximo']); ?></p>
                    <?php if ($clase['inscritos_actuales'] < $clase['cupo_maximo']): ?>
                        <button onclick="location.href='inscribirse.php?clase_id=<?php echo $clase['id']; ?>'">Inscribirse</button>
                    <?php else: ?>
                        <p class="cupo-lleno">Cupo Lleno</p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
