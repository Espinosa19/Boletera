<?php
require dirname(__DIR__, 2) . '/vendor/autoload.php';
require dirname(__DIR__, 2) . "/src/routes.php";

// Validar si la variable existe antes de usarla
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
         <link rel="stylesheet" href="../assets/css/complementos.css">
      <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
            <link rel="stylesheet" href="../assets/css/tablas.css">

    <title>Gestión de Categorías</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
            <?php include __DIR__ . '/complementos/header.php'; ?>

    <div class="container">
        <h1>Gestión de Categorías</h1>

        <button id="add-categoria">Agregar Categoría</button>

        <div id="categorias-container">
            <table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                        <th>Subcategorías</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($categorias)): ?>
    <tr>
        <td colspan="5">No hay categorías registradas.</td>
    </tr>
<?php else: ?>
    <?php foreach ($categorias as $cat): ?>
        <tr>
            <td><?= htmlspecialchars($cat['nombre'] ?? 'Sin Nombre') ?></td>
            <td><?= htmlspecialchars($cat['descripcion'] ?? 'Sin Descripción') ?></td>
            <td><?= htmlspecialchars($cat['estado'] ?? 'Desconocido') ?></td>
            <td>
                <?php if (!empty($cat['subcategorias'])): ?>
                    <ul>
                        <?php foreach ($cat['subcategorias'] as $sub): ?>
                            <li><?= htmlspecialchars($sub ?? 'Sin Nombre') ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <em>Sin subcategorías</em>
                <?php endif; ?>
            </td>
            <td>
                <!-- Asegúrate de tener el ID para poder operar -->
                <button class="btn btn-primary btn-sm edit-categoria" data-id="<?= htmlspecialchars((string) $cat['_id']) ?>">Editar</button>

                <button class="btn btn-danger btn-sm delete-categoria" data-id="<?= htmlspecialchars((string) $cat['_id']) ?>">Eliminar</button>
            </td>
        </tr>
    <?php endforeach; ?>
<?php endif; ?>

                </tbody>
            </table>
        </div>

        <form id="categoria-form" style="display: none; margin-top: 2rem;">
            <h2>Agregar Categoría</h2>

            <label for="nombre">Nombre de la Categoría:</label>
            <input type="text" id="nombre" name="nombre" required>

            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion" rows="3" required></textarea>

            <label for="estado">Estado:</label>
            <select id="estado" name="estado">
                <option value="activo">Activo</option>
                <option value="inactivo">Inactivo</option>
            </select>

            <h3>Subcategorías</h3>
            <div id="subcategorias-container">
                <div class="subcategoria">
                    <label for="subnombre">Nombre de la Subcategoría:</label>
                    <input type="text" name="subnombre[]" required>
                    <button type="button" class="remove-subcategoria">Eliminar Subcategoría</button>
                </div>
            </div>

            <button type="button" id="agregar-subcategoria">Agregar Subcategoría</button>

            <div style="margin-top: 1rem;">
                <button type="submit" id="submit-button">Guardar Categoría</button>
                <button type="button" id="cancel">Cancelar</button>
            </div>
        </form>
    </div>

    <script src="../assets/js/crud_categoria.js"></script>
    <script src="../assets/js/menu.js"></script>
</body>
</html>
