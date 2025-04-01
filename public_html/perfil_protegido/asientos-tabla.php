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
    <title>Gestión de Asientos</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
<style>
        /* Estilos generales */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 20px;
        }
        
        .container {
            max-width: 1100px;
            margin: auto;
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        /* Barra de búsqueda */
        #searchInput {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            margin-bottom: 15px;
            font-size: 16px;
        }

        /* Tabla */
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 14px;
            text-align: center;
        }

        th {
            background: #007bff;
            color: white;
            font-size: 16px;
        }

        tr:nth-child(even) {
            background: #f9f9f9;
        }

        tr:hover {
            background: #f1f1f1;
        }

        /* Botones */
        .btn {
            padding: 10px 15px;
            text-align: center;
            font-size: 14px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-success {
            background-color: #28a745;
            color: white;
        }

        .btn-success:hover {
            background-color: #218838;
        }

        .btn-danger {
            background-color: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

        .btn-edit {
            background-color: #ffc107;
            color: black;
        }

        .btn-edit:hover {
            background-color: #e0a800;
        }

        /* Estilos para select */
        select {
            padding: 8px;
            font-size: 14px;
            border-radius: 5px;
            border: 1px solid #ccc;
            outline: none;
            background: white;
        }

        /* Responsive */
        @media (max-width: 768px) {
            th, td {
                padding: 10px;
                font-size: 14px;
            }
            .btn {
                padding: 8px;
                font-size: 12px;
            }
        }
    </style>
    <div class="container">
        <h1>Gestión de Asientos</h1>
        <input type="text" id="searchInput" placeholder="Buscar asiento..." onkeyup="filtrarAsientos()">
        <a href="./asientos.php" class="btn btn-success">Agregar Nuevo Asiento</a>
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
                        <form method="POST" action="./apis/apia.php">
                            <select name="nuevo_estado">
                                <option value="Disponible" <?php echo isset($asiento['estado']) && $asiento['estado'] == 'Disponible' ? 'selected' : ''; ?>>Disponible</option>
                                <option value="Reservado" <?php echo isset($asiento['estado']) && $asiento['estado'] == 'Reservado' ? 'selected' : ''; ?>>Reservado</option>
                                <option value="Vendido" <?php echo isset($asiento['estado']) && $asiento['estado'] == 'Vendido' ? 'selected' : ''; ?>>Vendido</option>
                            </select>
                            <button class="btn btn-edit" value="<?php echo isset($asiento['_id']) ? $asiento['_id'] : ''; ?>" name="id">Actualizar</button>
                        </form>
                    </td>
                    <td>
                        <form method="POST" action="./apis/apia.php">
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
