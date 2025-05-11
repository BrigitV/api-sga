<?php
class CotizacionController
{
    public function cotizar($data)
    {
        // Validar datos requeridos
        $requiredFields = ['nombre', 'apellidos', 'fechaNacimiento', 'placa'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                return [
                    'status' => 400,
                    'data' => ['error' => "El campo $field es obligatorio"]
                ];
            }
        }
        // Validar formato de la placa
        if (!preg_match('/^[A-Z]{3}\d{3}$/i', $data['placa'])) {
            return [
                'status' => 400,
                'data' => ['error' => 'La placa debe tener formato ABC123']
            ];
        }
        // Enviar datos al api-aseguradora 
        $aseguradoraResponse = $this->callAseguradoraApi([
            'placa' => strtoupper($data['placa']) // Solo enviamos la placa
        ]);
        // Retornar la respuesta al frontend
        return $aseguradoraResponse;
    }
    private function callAseguradoraApi($data)
    {
        $url = 'http://localhost/proyecto-seguros/api-aseguradora/cotizar'; // Ajusta esta URL

        // Configurar la petición HTTP
        $options = [
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/json\r\n",
                'content' => json_encode($data)
            ]
        ];
        $context = stream_context_create($options);
        $response = @file_get_contents($url, false, $context);
        // Manejar errores de conexión
        if ($response === false) {
            return [
                'status' => 500,
                'data' => ['error' => 'Error al conectar con el servicio de aseguradora']
            ];
        }
        return json_decode($response, true);
    }
}
