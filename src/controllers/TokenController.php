<?php
require dirname(__DIR__, 2) . '/vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Dotenv\Dotenv;

class TokenController {
    private static $clave = null;
    
    private static function init() {
        if (self::$clave === null) {
            $dotenv = Dotenv::createImmutable(dirname(__DIR__,2));
            $dotenv->load();
            self::$clave = $_ENV['SECURITY_JWT'] ?? null;

            if (!self::$clave) {
                die("Error: JWT_SECRET no está definido en el archivo .env");
            }
        }
    }

    public static function generarToken($usuario_id,$datos_token) {
        self::init(); 
        $tiempo_actual = time();
        $expiracion = $tiempo_actual + (60 * 60); // 1 hora

        $datos = [
            "iat" => $tiempo_actual,
            "exp" => $expiracion,
            "sub" => $usuario_id,
            "data"=> $datos_token
        ];

        return JWT::encode($datos, self::$clave, 'HS256');
    }
    public static function validarToken($token) {
        self::init(); 
    
        try {
            // Decodificar el token directamente
            $decoded = JWT::decode($token, new Key(self::$clave, 'HS256'));
    
            // Verificar los permisos según el rol
            if (!isset($decoded->data->usuario->rol)) {
                return ['acceso' => "denegado"];
            }
    
            $rol = $decoded->data->usuario->rol;
    
            // Definir permisos según el rol
            $permisosPorRol = [
                'admin' => ['crear_usuario', 'eliminar_usuario', 'editar_usuario', 'ver_boletos'],
                'boletero' => ['ver_boletos', 'validar_boletos'],
            ];
    
            // Si el rol no está en la lista, denegar acceso
            if (!array_key_exists($rol, $permisosPorRol)) {
                return ['acceso' => "denegado"];
            }
    
            // Retornar el rol y los permisos asociados
            return [
                'acceso' => "permitido",
                'rol' => $rol,
                'permisos' => $permisosPorRol[$rol]
            ];
        } catch (\Exception $e) {
            return ['error' => "Error al validar token: " . $e->getMessage()];
        }
    }
    
    
}

?>