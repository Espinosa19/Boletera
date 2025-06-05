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
         <link rel="stylesheet" href="../assets/css/complementos_es.css">
         <link rel="stylesheet" href="../assets/css/estilos_es.css">
      <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
            <link rel="stylesheet" href="../assets/css/tablas_es.css">

    <title>Gestión de Usuarios</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
            <?php include __DIR__ . '/complementos/header.php'; ?>

    <div class="container">
        <h1>Gestión de Usuarios</h1>

<button id="add-user" style="margin-top: 1rem;">Agregar Usuario</button>

        <div id="usuarios-container">
            <table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Correo Electrónico</th>
                        <th>Teléfono</th>
                        <th>Rol</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($usuarios)): ?>
    <tr>
        <td colspan="5">No hay usuarios registrados.</td>
    </tr>
<?php else: ?>
    <?php foreach ($usuarios as $usuario): ?>
        <tr>
            <td><?= htmlspecialchars($usuario['nombre'] ?? 'Sin Nombre') ?></td>
            <td><?= htmlspecialchars($usuario['email'] ?? 'Sin Correo') ?></td>
            <td><?= htmlspecialchars($usuario['telefono'] ?? 'Sin Teléfono') ?></td>
            <td>
                <?php if (!empty($usuario['role'])): ?>
                    <em><?= htmlspecialchars($usuario['role']) ?></em>
                <?php endif; ?>
            </td>
            <td>
                <!-- Asegúrate de tener el ID para poder operar -->
                <button class="btn btn-primary btn-sm edit-usuario" data-id="<?= htmlspecialchars((string) $usuario['_id']) ?>">Editar</button>

                <button class="btn btn-danger btn-sm delete-usuario" data-id="<?= htmlspecialchars((string) $usuario['_id']) ?>">Eliminar</button>
            </td>
        </tr>
    <?php endforeach; ?>
<?php endif; ?>

                </tbody>
            </table>
        </div>
<!-- Botón para mostrar el formulario de usuario -->

<!-- Formulario para agregar usuario -->
<form id="user-form" style="display: none; margin-top: 2rem;">
    <h2>Agregar Usuario</h2>

    <label for="nombre-usuario">Nombre:</label>
    <input type="text" id="nombre-usuario" name="nombre" required>

    <label for="telefono-usuario">Teléfono:</label>
    <input type="tel" id="telefono-usuario" name="telefono" required>

    <label for="email-usuario">Correo electrónico:</label>
    <input type="email" id="email-usuario" name="email" required>

    <label for="password-usuario">Contraseña:</label>
    <input type="password" id="password-usuario" name="password" required>

    <select id="role" name="role" required>
        <option value="user">Usuario</option>
        <option value="organizer">Organizador</option>
        <option value="admin">Administrador</option>
        <option value="validador">Validador</option> <!-- Opcional -->
    </select>

    <div style="margin-top: 1rem;">
        <button type="submit" id="guardar-usuario">Guardar Usuario</button>
        <button type="button" id="cancel-user">Cancelar</button>
    </div>
</form>

    </div>

    <script src="../assets/js/crud_usuario.js"></script>
    <script src="../assets/js/menu.js"></script>
</body>
</html>
