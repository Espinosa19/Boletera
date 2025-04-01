<?php
session_start();
require_once __DIR__ . '/controllers/EventoController.php';
require_once __DIR__ . '/controllers/RecintoController.php';
require_once __DIR__ . '/controllers/PagoController.php';
require_once __DIR__ . '/controllers/TipoAsientoController.php';
require_once __DIR__ . '/controllers/AsientoController.php';
require_once __DIR__ . '/controllers/TokenController.php';
require_once __DIR__ . '/controllers/CompraController.php';

$basePath = "/administrador/public_html";
$eventoController = new EventoController();
$recintoController = new RecintoController();
$pagoController = new PagoController();
$tipoController = new TipoAsientoController();
$asientoController = new AsientoController();
$tokenController = new TokenController();
$compraController = new CompraController();
$uri = trim(str_replace($basePath, '', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)), "/");

if(!empty($_SESSION['datos-token'])){

    // Rutas principales
    if ($uri == '' || $uri == 'login') {
        echo "Bienvenido";
    } 
    // Eventos
    else if ($uri == 'perfil_protegido/eventos.php') {
        $permisos = $tokenController->validarToken($_SESSION['datos-token']);
        verificacionAcceso($permisos);
        $resultados = $eventoController->listarEventos();
        $eventos = $resultados['eventos'];
        $recintos = $resultados['recintos'];
    } 
    // Recintos
    else if ($uri == 'perfil_protegido/recintos.php') {
        $permisos = $tokenController->validarToken($_SESSION['datos-token']);
        verificacionAcceso($permisos);
        $recintos = $recintoController->obtenerRecintos();
    } 
    else if ($uri == 'perfil_protegido/pagos.php') {
        $permisos = $tokenController->validarToken($_SESSION['datos-token']);
        verificacionAcceso($permisos);
        $pagos = $pagoController->index();
    } 
    else if ($uri == 'perfil_protegido/tipo_asiento.php') {
        $permisos = $tokenController->validarToken($_SESSION['datos-token']);
        verificacionAcceso($permisos);
        $tipos = $tipoController->obtenerTiposAsientos();
    }
    else if ($uri == 'perfil_protegido/asientos.php') {
        $permisos = $tokenController->validarToken($_SESSION['datos-token']);
        verificacionAcceso($permisos);
        $asientos = $asientoController->obtenerTodos();
        $eventos=$eventoController->listar();
        $tipos = $tipoController->obtenerTiposAsientos();
    } 
    else if ($uri == 'perfil_protegido/asientos-tabla.php') {
        $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
        $permisos = $tokenController->validarToken($_SESSION['datos-token']);
        verificacionAcceso($permisos);
        $limite = 10; // Cambia esto según lo que necesites

        $asientos = $asientoController->obtenerTodos($pagina, $limite);
    } 
    else if ($uri =='perfil_protegido/compra.php') {
        $permisos = $tokenController->validarToken($_SESSION['datos-token']);
        verificacionAcceso($permisos);
        $eventos = $eventoController->listar();
    } 
    // Manejo de error 404
    else {
        http_response_code(404);
        echo "Página no encontrada";
    }
}else{
    header("location: ../login.php");
    exit;
}

function verificacionAcceso($permisos){
    if (isset($permisos['acceso']) && $permisos['acceso'] == "denegado") {
        http_response_code(403);  // Código HTTP 403 - Forbidden
        echo "Acceso denegado: No tiene permisos para acceder a esta página";
        exit;  // Detener la ejecución después de enviar la respuesta
    }
}
?>
