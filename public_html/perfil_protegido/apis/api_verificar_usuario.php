<?php
require dirname(__DIR__,2) . '/vendor/autoload.php';
require dirname(__DIR__). '/conexion/generarConexion.php';
require dirname(__DIR__).'/email.php';
use Firebase\JWT\JWT;
$json = file_get_contents('php://input');
$datos = json_decode($json, true);

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    if (isset($datos['correo'], $datos['contra']) && !empty($datos['correo']) && !empty($datos['contra'])) {
        
        $correo = filter_var($datos['correo'], FILTER_SANITIZE_EMAIL);
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['error' => "El correo electrónico no es válido"]);
            exit;
        }
        $password = htmlspecialchars($datos['contra'], ENT_QUOTES, 'UTF-8');
        $consultaDatos = $usuarios->findOne(['email' => $correo]);
        if ($consultaDatos !== null) {
            if ($password== $consultaDatos['password']){
                $jwt=generarJWT($consultaDatos['nombre'],$consultaDatos['_id']);
                enviarEmail($correo,$jwt);
                echo json_encode([
                    'jwt'=>$jwt,
                    'mensaje' => "Inicio de sesión exitoso",
                    'status' => true
                ]);
            } else {
                echo json_encode(['error' => "La contraseña es incorrecta"]);
            }
        } else {
            echo json_encode(['error' => "El correo electrónico no está registrado"]);
        }
    } else {
        echo json_encode(['error' => "Debe proporcionar correo y contraseña"]);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => "Método no permitido"]);
}
function generarJWT($nombre,$id){
    $secret=$_ENV['SECURITY_JWT'];
    $issuedAt = time();
    $expirationTime = $issuedAt + 3600; 
    $payload = [
        'iat' => $issuedAt,
        'exp' => $expirationTime,
        'role' => 'admin'
    ];
    $jwt=JWT::encode($payload,$secret,'HS256');
    return $jwt;
}
?>
