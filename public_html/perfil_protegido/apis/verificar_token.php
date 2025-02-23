<?php
session_start();
require dirname(__DIR__, 3) .'/src/controllers/TokenController.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json");

$tokenController = new TokenController();

if (!isset($_SESSION['datos-token']['token'])) {
    echo json_encode(["error" => "Token no encontrado en la sesión"]);
    exit;
}

$esValido = $tokenController->validarToken($_SESSION['datos-token']['token']);

if ($esValido) {
    echo json_encode(["status" => "Token válido"]);
} else {
    echo json_encode(["error" => "Token inválido o expirado"]);
}?>
