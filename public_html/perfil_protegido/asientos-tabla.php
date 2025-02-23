<?php  

require dirname(__DIR__,2) . '/vendor/autoload.php';
require dirname(__DIR__,2) . "/src/routes.php";


?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Asientos</title>
    <link rel="stylesheet" href="css/estilos.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .btn {
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .btn-danger {
            background-color: #dc3545;
        }
        .btn-danger:hover {
            background-color: #c82333;
        }
        .btn-success {
            background-color: #28a745;
        }
        .btn-success:hover {
            background-color: #218838;
        }
        form {
            display: inline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Gestión de Asientos</h1>
        <input type="text" id="searchInput" placeholder="Buscar asiento..." style="width: 100%; padding: 10px; margin-bottom: 10px;">

        <a href="crear_asientos.php" class="btn btn-success">Agregar Nuevo Asiento</a>
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
            <?php foreach ($asientos as $asiento): ?>
                <tr>
                    <td><?php echo htmlspecialchars($asiento['nombre_recinto']); ?></td> <!-- Mostrar nombre del recinto -->
                    <td><?php echo htmlspecialchars($asiento['tipo_asiento']['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($asiento['zona']); ?></td>
                    <td><?php echo htmlspecialchars($asiento['fila']); ?></td>
                    <td><?php echo htmlspecialchars($asiento['numero']); ?></td>
                    <td><?php echo htmlspecialchars($asiento['estado']); ?></td>
                    <td>
                        <!-- Botón para editar el estado del asiento -->
                        
                           
                            <select name="nuevo_estado">
                                <option value="disponible" <?php echo $asiento['estado'] == 'disponible' ? 'selected' : ''; ?>>Disponible</option>
                                <option value="reservado" <?php echo $asiento['estado'] == 'reservado' ? 'selected' : ''; ?>>Reservado</option>
                                <option value="vendido" <?php echo $asiento['estado'] == 'vendido' ? 'selected' : ''; ?>>Vendido</option>
                            </select>
                        </td>
                        <td>
                        <form method="POST" action="./apis/apia.php">
                        <button value="<?php echo $asiento['_id']; ?>" name="id" id="editar-asiento">Editar</button>
                        </form>
                            <button class="btn btn-danger" id="eliminar-asiento" value="<?php echo $asiento['_id']; ?>">Eliminar</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <script src="./../assets/js/asientos-tabla.js"></script>
</body>
</html>
