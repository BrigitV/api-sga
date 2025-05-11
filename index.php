<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
require_once 'controllers/CotizacionController.php';

// Obtener la ruta solicitada
$request_uri = $_SERVER['REQUEST_URI'];
$base_path = '/proyecto-seguros/api-sga'; 
// Extraer el endpoint
$endpoint = str_replace($base_path, '', $request_uri);
$endpoint = explode('?', $endpoint)[0]; // Eliminar parámetros

// Manejar el endpoint /cotizar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $endpoint === '/cotizar') {
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        
        // Validar que se recibió JSON válido
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Formato JSON inválido');
        }
        
        $controller = new CotizacionController();
        $response = $controller->cotizar($input);

        echo json_encode($response);
        
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    // Endpoint no encontrado
    http_response_code(404);
    echo json_encode(['error' => 'Endpoint no encontrado']);
}