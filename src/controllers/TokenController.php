<?php
require dirname(__DIR__, 2) . '/vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Dotenv\Dotenv;

class TokenController {
    private static $clave = null;
    private $boletoController;

    
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
    public function generarClave($datos_zona, $evento, $id) {
        self::init();

        if (empty($evento) || empty($id)) {
            throw new InvalidArgumentException("Faltan datos obligatorios: evento o ID del cliente.");
        }
    
        if (!is_array($datos_zona) || count($datos_zona) === 0) {
            throw new InvalidArgumentException("Se requiere al menos un asiento para generar la clave del boleto.");
        }
    
    

        $tokens = [];
    
        foreach ($datos_zona as $dato) {
            if (!isset($dato['zona'], $dato['asiento'], $dato['tipo'])) {
                throw new InvalidArgumentException("Faltan datos obligatorios en un asiento (zona, asiento o tipo).");
            }
    
            $payload = [
                "event" => $evento,
                "client_id" => $id,
                "zona" => $dato['zona'],
                "fila" => $dato['fila'] ?? null,
                "asiento" => $dato['asiento'],
                "tipo_asiento" => $dato['tipo'],
                "timestamp" => time() // Agrega un timestamp para evitar repetidos
            ];
    
            try {
                $jwt = JWT::encode($payload, self::$clave, 'HS256');
                $tokens[] = $jwt;
            } catch (Exception $e) {
                throw new RuntimeException("Error al generar el token JWT: " . $e->getMessage());
            }
        }
    
        return $tokens; // Devuelve un array con los tokens generados para cada boleto
    }
    public static function generarCodigoBarras($datos_boleto, $evento, $idc) {
        self::init();
    
        // Validación del evento
        if (!is_string($evento) || strlen($evento) > 255) {
            throw new InvalidArgumentException("El evento debe ser un string de máximo 255 caracteres.");
        }
    
        // Validación de ID cliente
        if (empty($idc) || !is_string($idc)) {
            throw new InvalidArgumentException("El ID del cliente es inválido.");
        }
    
        // Verificar que $datos_boleto es un array y no está vacío
        if (!is_array($datos_boleto) || count($datos_boleto) === 0) {
            throw new InvalidArgumentException("Se requiere al menos un boleto para generar el código de barras.");
        }
    
        $codigosBarras = [];
    
        // Procesar cada boleto en el array
        foreach ($datos_boleto as $dato) {
            if (!isset($dato['zona'], $dato['asiento'], $dato['tipo'])) {
                throw new InvalidArgumentException("Faltan datos obligatorios en un boleto (zona, asiento o tipo).");
            }
    
            // Validaciones de tipo de datos
            if (!is_string($dato['zona']) || strlen($dato['zona']) > 100) {
                throw new InvalidArgumentException("La zona debe ser un string de máximo 100 caracteres.");
            }
            if (!is_numeric($dato['asiento']) || $dato['asiento'] <= 0) {
                throw new InvalidArgumentException("El número de asiento debe ser un número positivo.");
            }
    
            // Validación opcional de fila
            if (isset($dato['fila']) && (!is_string($dato['fila']) || strlen($dato['fila']) > 10)) {
                throw new InvalidArgumentException("La fila debe ser un string de máximo 10 caracteres.");
            }
    
            // Validación opcional de tipo de boleto
            $tipo_boleto = $dato['tipo'] ?? '';
            if (!is_string($tipo_boleto) || strlen($tipo_boleto) > 50) {
                throw new InvalidArgumentException("El tipo de boleto debe ser un string de máximo 50 caracteres.");
            }
    
            // Construcción del payload para el token
            $payload = [
                "evento" => $evento,
                "cliente_id" => $idc,
                "tipo_boleto" => $tipo_boleto,
                "zona" => $dato['zona'],
                "fila" => $dato['fila'] ?? null,
                "asiento" => $dato['asiento'],
                "timestamp" => time() // Agrega un timestamp para evitar repetidos
            ];
            
            // Generar token y código de barras único
            $token = JWT::encode($payload, self::$clave, 'HS256');
            $codigoBarras = substr(hash('sha256', $token), 0, 16); // Código único basado en hash
    
            $codigosBarras[] = $codigoBarras;
        }
    
        return $codigosBarras; // Devuelve un array con los códigos de barras generados
    }
    
}

?>