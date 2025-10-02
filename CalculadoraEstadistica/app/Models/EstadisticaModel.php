<?php
// app/Models/EstadisticaModel.php

/**
 * Clase EstadisticaModel (POO): Contiene la lógica para recuperar datos y realizar cálculos estadísticos.
 */
class EstadisticaModel
{ // <-- INICIO DE LA CLASE (POO: Encapsulación de la lógica de negocio)
    private $dbManager;

    public function __construct(DbManager $dbManager)
    { // <-- MÉTODO CONSTRUCTOR (POO)
        // POO: Dependencia del DbManager (Inyección de Dependencias)
        $this->dbManager = $dbManager;
    }

    /**
     * Realiza la consulta de datos y calcula la media y desviación estándar.
     */
    public function calcularEstadisticas(string $table, string $column): ?array
    {
        $conn = $this->dbManager->getConnection();
        
        if (!$conn) {
            return ['error' => "Error de conexión con la base de datos."];
        }
        
        // Saneamiento de nombres
        $safe_table = preg_replace('/[^a-zA-Z0-9_]/', '', $table);
        $safe_column = preg_replace('/[^a-zA-Z0-9_]/', '', $column);

        // La consulta que se intenta ejecutar
        $query = "SELECT {$safe_column} FROM {$safe_table}";
        
        // --- INICIO DE DEPURACIÓN CRÍTICA ---
        echo "DEBUG: Query a ejecutar: " . htmlspecialchars($query) . "<br>"; // Muestra la consulta
        echo "DEBUG: Intentando ejecutar consulta...<br>"; // Punto de ejecución
        // --- FIN DE DEPURACIÓN CRÍTICA ---

        try {
            $startTime = microtime(true);
            
            // LA LÍNEA CRÍTICA
            $stmt = $conn->query($query);
            
            echo "DEBUG: Consulta ejecutada con éxito. Intentando obtener datos...<br>"; // Si llega aquí, la consulta funciona
            
            $raw_data = $stmt->fetchAll(PDO::FETCH_COLUMN);

            echo "DEBUG: Datos obtenidos. Cantidad de filas: " . count($raw_data) . "<br>"; // Si llega aquí, la lectura funciona

        } catch (PDOException $e) {
            error_log("Error en la consulta SQL: " . $e->getMessage());
            return ['error' => "Error de consulta. Tabla o columna no encontrada: " . $e->getMessage()];
        }

        $datos_numericos = [];
        // REQUISITO: Manejar valores nulos o no numéricos
        foreach ($raw_data as $value) {
            if (is_numeric($value) && $value !== null) {
                // REQUISITO: Manejo de notación científica y valores extremos
                $datos_numericos[] = (float)$value;
            }
        }

        $n = count($datos_numericos);
        if ($n === 0) {
            return ['error' => "No hay datos numéricos válidos para procesar."];
        }

        // --- CÁLCULO DE LA MEDIA (μ) ---
        // REQUISITO: Cálculo de media aritmética
        $suma_valores = array_sum($datos_numericos);
        $media = $suma_valores / $n;

        // --- CÁLCULO DE LA DESVIACIÓN ESTÁNDAR POBLACIONAL (σ) ---
        // REQUISITO: Cálculo de desviación estándar
        $suma_diferencias_cuadrado = 0;
        foreach ($datos_numericos as $xi) {
            $diff = $xi - $media;
            $suma_diferencias_cuadrado += pow($diff, 2);
        }
        $varianza = $suma_diferencias_cuadrado / $n;
        $desviacion_estandar = sqrt($varianza);

        $endTime = microtime(true);
        $processingTime = $endTime - $startTime;

        // REQUISITO: Estadísticas básicas (Mínimo, Máximo, Rango)
        $min_val = min($datos_numericos);
        $max_val = max($datos_numericos);
        $range = $max_val - $min_val;

        // REQUISITO DE PRECISIÓN: Formato de 6 decimales
        $resultados = [
            'media' => number_format($media, 6, '.', ''),
            'desviacion_estandar' => number_format($desviacion_estandar, 6, '.', ''),
            'num_procesados' => $n,
            'minimo' => $min_val,
            'maximo' => $max_val,
            'rango' => $range,
            'tiempo_procesamiento' => $processingTime,
        ];

        echo "DEBUG: Cálculo completado. Retornando resultados.<br>";
        
        return $resultados;
    }
}
