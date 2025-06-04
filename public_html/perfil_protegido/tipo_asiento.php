<?php
    
require dirname(__DIR__,2) . '/vendor/autoload.php';
require dirname(__DIR__,2) . "/src/routes.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <link rel="stylesheet" href="../assets/css/complementos.css">
      <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <title>Gestión de Tipos de Asientos</title>
    <style>
        /* Estilos generales */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .table th, .table td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }

        .btn {
            padding: 8px 12px;
            cursor: pointer;
            border: none;
            color: white;
            background-color: #007bff;
            font-size: 16px;
        }

        .btn-danger {
            background-color: #dc3545;
        }

        .btn-warning {
            background-color: #ffc107;
        }

        .btn-success {
            background-color: #28a745;
        }

        /* Estilos del modal */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 999;
        }

        .modal {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            width: 400px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-title {
            font-size: 20px;
            font-weight: bold;
        }

        .btn-close {
            cursor: pointer;
            background-color: transparent;
            border: none;
            font-size: 18px;
        }

        .modal-body {
            margin-top: 20px;
        }

        .modal-body input, .modal-body select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
        }

        /* Animación de apertura del modal */
        .open {
            display: flex;
        }

        .btn-close:focus {
            outline: none;
        }
    </style>
</head>
<body>
        <?php include __DIR__ . '/complementos/header.php'; ?>

    <div class="container">
        <h2>Tipos de Asientos</h2>
        <!-- Botón para abrir el modal de nuevo asiento -->
        <button class="btn" id="nuevoAsientoBtn">Nuevo Asiento</button>
    
        <table class="table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Creado Por</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="tablaAsientos">
                <?php
                    foreach ($tipos as $tipo) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($tipo['nombre']) . "</td>";
                        echo "<td>" . htmlspecialchars($tipo['precio']) . "</td>";
                        echo "<td>" . htmlspecialchars($tipo['creado_por']) . "</td>";
                        echo "<td>" . ($tipo['activo'] ? "Activo" : "Inactivo") . "</td>";
                        echo "<td>
                            <button class='btn btn-warning' id='editarBtn' value='".$tipo['_id']."' data-id='".$tipo['_id']."'>Editar</button>
                            <button class='btn btn-danger' id='eliminarBtn' value='".$tipo['_id']."' data-id='".$tipo['_id']."'>Eliminar</button>
                        </td>";
                        echo "</tr>";
                    }
                ?>
            </tbody>
        </table>
    </div>

    <!-- MODAL -->
    <div class="modal-overlay" id="modalAsiento">
        <div class="modal">
            <div class="modal-header">
                <h5 class="modal-title">Tipo de Asiento</h5>
                <button type="button" class="btn-close" id="cerrarModal">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="asientoId">
                <div class="mb-3">
                    <label>Nombre</label>
                    <input type="text" id="nombre" class="form-control">
                </div>
                <div class="mb-3">
                    <label>Precio</label>
                    <input type="number" id="precio" class="form-control">
                </div>
                <div class="mb-3">
                    <label>Creado Por</label>
                    <input type="number" id="creadoPor" class="form-control">
                </div>
                <div class="mb-3">
                    <label>Activo</label>
                    <select id="activo" class="form-control">
                        <option value="true">Sí</option>
                        <option value="false">No</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success" id="guardarBtn">Guardar</button>
                </div>
            </div>
        </div>
    </div>
    <script src="../assets/js/crud_tipoasiento.js"></script>
</body>
</html>
