<?php

require dirname(__DIR__,2) . '/vendor/autoload.php';
require dirname(__DIR__,2) . "/src/routes.php";

// Definir la cantidad de asientos por página
$asientosPorPagina = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $asientosPorPagina;

// Obtener total de asientos
$totalAsientos = count($asientos);
$totalPaginas = ceil($totalAsientos / $asientosPorPagina);

// Obtener los asientos para la página actual
$asientosPaginados = array_slice($asientos, $offset, $asientosPorPagina);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="../assets/css/complementos_es.css">
                 <link rel="stylesheet" href="../assets/css/tablas_es.css">
    <link rel="stylesheet" href="../assets/css/estilos_es.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <title>Gestión de Asientos</title>
</head>
<body>
            <?php include __DIR__ . '/complementos/header.php'; ?>

    <div class="container">
        <h1>Gestión de Asientos</h1>
        <input type="text" id="searchInput" placeholder="Buscar asiento..." onkeyup="filtrarAsientos()">
        <a href="./asientos.php" class="btn btn-success">Agregar Nuevo Asiento</a>
        <a href="./asientos-reiniciar.php" class="btn btn-success">Reiniciar Asientos</a>
        <table>
            <thead>
                <tr>
                    <th>Recinto</th>
                    <th>Tipo de Asiento</th>
                    <th>Zona</th>
                    <th>Fila</th>
                    <th>Número</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="asientosTable">
                <?php foreach ($asientosPaginados as $asiento): ?>
                <tr>
                    <td><?php echo htmlspecialchars($asiento['nombre_recinto']); ?></td>
                    <td><?php echo htmlspecialchars($asiento['tipo_asiento']['nombre'] ?? "No se encontró"); ?></td>
                    <td><?php echo htmlspecialchars($asiento['zona'] ?? "No se encontró la zona"); ?></td>
                    <td><?php echo htmlspecialchars($asiento['fila'] ?? "Sin fila"); ?></td>
                    <td><?php echo htmlspecialchars($asiento['numero'] ?? "Sin asiento"); ?></td>
                    <td>
                        <form method="POST" style="display:block" action="./apis/apia.php">
                            <select name="nuevo_estado">
                                <option value="Disponible" <?php echo isset($asiento['estado']) && $asiento['estado'] == 'Disponible' ? 'selected' : ''; ?>>Disponible</option>
                                <option value="Reservado" <?php echo isset($asiento['estado']) && $asiento['estado'] == 'Reservado' ? 'selected' : ''; ?>>Reservado</option>
                                <option value="Vendido" <?php echo isset($asiento['estado']) && $asiento['estado'] == 'Vendido' ? 'selected' : ''; ?>>Vendido</option>
                            </select>
                            <button class="btn btn-edit" value="<?php echo isset($asiento['_id']) ? $asiento['_id'] : ''; ?>" name="id">Actualizar</button>
                        </form>
                    </td>
                    <td>
                        <form method="POST" style="display:flex" action="./apis/apia.php">
                            <input type="hidden" name="_method" value="DELETE">
                            <button class="btn btn-danger" name="id" value="<?php echo $asiento['_id']; ?>">Eliminar</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <!-- Paginación -->
        <div style="text-align: center; margin-top: 20px;">
    <?php if ($pagina > 1): ?>
        <a href="?pagina=<?php echo $pagina - 1; ?>" class="btn btn-primary">← Anterior</a>
    <?php endif; ?>

    <span style="margin: 0 10px;">Página <?php echo $pagina; ?></span>

    <a href="?pagina=<?php echo $pagina + 1; ?>" class="btn btn-primary">Siguiente →</a>
</div>

    </div>

    <script src="./../assets/js/asientos-tabla.js"></script>
</body>
</html>
