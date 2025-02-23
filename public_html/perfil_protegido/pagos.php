<?php
    
require dirname(__DIR__,2) . '/vendor/autoload.php';
require dirname(__DIR__,2) . "/src/routes.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Pagos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        
        h1, h2 {
            text-align: center;
            color: #333;
        }
        
        #toggleFormButton {
            display: block;
            margin: 20px auto;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            font-size: 16px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }
        
        #toggleFormButton:hover {
            background-color: #0056b3;
        }
        
        form {
            background: white;
            max-width: 400px;
            margin: 20px auto;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            display: none;
        }
        
        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }
        
        input, button {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        
        button {
            background-color: #28a745;
            color: white;
            font-weight: bold;
            cursor: pointer;
            border: none;
        }
        
        button:hover {
            background-color: #218838;
        }
        
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            background: white;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        
        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }
        
        th {
            background-color: #007bff;
            color: white;
        }
        
        tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head><body>
    <h1>Gestión de Pagos</h1>

    
    <!-- Campo de búsqueda -->
    <label for="buscarFecha"><strong>Buscar por fecha:</strong></label>
    <input type="text" id="buscarFecha" placeholder="Ej: 2024-08-21 23:07">
    
    <h2>Lista de Pagos</h2>
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody id="pagoTableBody">
            <?php foreach($pagos as $pago): ?>
                <tr>
                    <td><?php echo htmlspecialchars($pago['nombre_usuario']); ?></td>
                    <td><?php echo htmlspecialchars($pago['fecha']->toDateTime()->format('Y-m-d H:i:s')); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

   

    <script src="../assets/js/crud_pagos.js"></script>
</body>
</html>
