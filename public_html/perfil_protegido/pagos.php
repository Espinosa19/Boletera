<?php
    
require dirname(__DIR__,2) . '/vendor/autoload.php';
require dirname(__DIR__,2) . "/src/routes.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
           <link rel="stylesheet" href="../assets/css/complementos_es.css">
                 <link rel="stylesheet" href="../assets/css/tablas_es.css">
    <link rel="stylesheet" href="../assets/css/estilos_es.css">

    <title>CRUD Pagos</title>
    <style>
        
    </style>
</head><body>
        <?php include __DIR__ . '/complementos/header.php'; ?>
    <div class="container">
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

            </div>

    <script src="../assets/js/crud_pagos.js"></script>
</body>
</html>
