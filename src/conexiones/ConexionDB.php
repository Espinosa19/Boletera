<?php
require dirname(__DIR__, 2) . '/vendor/autoload.php';

use Dotenv\Dotenv;
use MongoDB\Client;

class ConexionDB {
    private static $conn = null;

    public static function conectar() {
        if (self::$conn === null) {
            $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
            $dotenv->load();

            $uri = $_ENV['MONGO_DB']; 
            $dbname = $_ENV['DB_NAME']; 
            
            try {
                $client = new Client($uri);
                
                self::$conn = $client->selectDatabase($dbname);
            } catch (\Exception $e) {
                die("Error de conexión a MongoDB: " . $e->getMessage());
            }
        }
        return self::$conn;
    }
}
?>
