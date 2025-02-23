<?php
session_start();

require dirname(__DIR__, 3) .'/src/controllers/TokenController.php';
require dirname(__DIR__, 3) . '/src/controllers/UsuarioController.php';
require dirname(__DIR__,3).'/src/controllers/EmailController.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json");

$usuarioController = new UsuarioController();
$tokenController = new TokenController();
$emailController=new EmailController();
// Obtener datos del cuerpo de la petición
$datos = json_decode(file_get_contents("php://input"), true);

if (!isset($datos['correo']) || !isset($datos['contra'])) {
    echo json_encode(["error" => "Faltan datos"]);
    exit;
}

// Filtrar entrada (Prevención de SQL Injection y XSS)
$email = filter_var($datos['correo'], FILTER_SANITIZE_EMAIL);
$password = trim($datos['contra']); // Elimina espacios en blanco adicionales

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["error" => "Correo electrónico no válido"]);
    exit;
}

$usuario = $usuarioController->obtenerUsuarioPorEmail($email);
try{


if ($usuario && password_verify($password, $usuario['password'])) {
    $datos=[
        "usuario" => [
            "id" => $usuario['_id'],
            "nombre" => htmlspecialchars($usuario['nombre']),
            "email" => htmlspecialchars($usuario['email']),
            "rol" => htmlspecialchars($usuario['role'])
        ]
    ];
    $token = $tokenController->generarToken($usuario['_id'],$datos);
    $_SESSION['datos-token']=$token;
    $clave=generarIdUnico();
    $ema=$emailController->enviarEmail($email,$clave);
    
    echo json_encode(['status'=>'login']);
} else {
    echo json_encode(["error" => "Credenciales incorrectas"]);
}
}catch(Exception $e){
    echo json_encode(['error' => 'Ocurrió un error al procesar la solicitud', 'detalle' => $e->getMessage()]);
}
function generarIdUnico() {
    return bin2hex(random_bytes(24));
}
?>
