<?php
// app/Controllers/CalculoController.php (VERSIÓN MODIFICADA)

require_once __DIR__ . '/../Models/DbManager.php';
require_once __DIR__ . '/../Models/EstadisticaModel.php';

class CalculoController
{

    
    private $dbManager;
    private $model;
    private $config;
    private $driver;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    // --- NUEVO MÉTODO: Obtiene las columnas de una tabla específica. ---
    private function getColumns(string $table): array
    {
        $conn = $this->dbManager->getConnection();
        if (!$conn || empty($table)) {
            return ['error' => "No hay conexión o tabla seleccionada."];
        }

        // REQUISITO DE SEGURIDAD: Saneamiento del nombre de la tabla
        $safe_table = preg_replace('/[^a-zA-Z0-9_]/', '', $table);

        try {
            // Consulta SQL para obtener la lista de columnas (ejemplo para MySQL)
            $query = "SHOW COLUMNS FROM {$safe_table}";
            $stmt = $conn->query($query);

            $raw_columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $columns = [];

            // Extraer solo los nombres de las columnas
            foreach ($raw_columns as $col) {
                $columns[] = $col['Field'];
            }
            return $columns;
        } catch (PDOException $e) {
            error_log("Error al obtener columnas: " . $e->getMessage());
            return ['error' => "Error al obtener columnas de la tabla '{$table}'."];
        }
    }

    // ... (El método getTables() permanece igual) ...
    private function getTables(): array
    {
        $conn = $this->dbManager->getConnection();
        if (!$conn) {
            return ['error' => "Fallo de conexión. Revise los parámetros de la BD."];
        }

        try {
            if ($this->driver === 'mysql') {
                $stmt = $conn->query("SHOW TABLES");
                return $stmt->fetchAll(PDO::FETCH_COLUMN);
            }
            return ["datos_ejemplo_1", "registros_metricos"];
        } catch (PDOException $e) {
            error_log("Error al obtener tablas: " . $e->getMessage());
            return ['error' => "Error al obtener tablas de la base de datos."];
        }
    }


    public function index()
    {
        $driver = $_POST['driver'] ?? 'mysql';
        $this->driver = $driver;
        // Obtenemos la tabla previamente seleccionada (si la hay)
        $selected_table = $_POST['table'] ?? '';

        $this->dbManager = new DbManager($this->config);
        $this->model = new EstadisticaModel($this->dbManager);

        $connection_successful = $this->dbManager->connect($driver);
        $tables = $connection_successful ? $this->getTables() : ['error' => 'No se pudo conectar para listar las tablas.'];

        // Cargar las columnas solo si la conexión es exitosa Y hay una tabla seleccionada
        $columns = [];
        if ($connection_successful && !empty($selected_table) && !isset($tables['error'])) {
            $columns = $this->getColumns($selected_table);
        }

        // Se pasa la tabla seleccionada y las columnas a la vista
        include __DIR__ . '/../../views/index.php';
    }

    public function calcular()
    {

        $driver = $_POST['driver'] ?? 'mysql';
        $this->driver = $driver;
        $table = $_POST['table'] ?? '';
        $column = $_POST['column'] ?? '';
        $error_message = null;
        $resultados = null;

        $this->dbManager = new DbManager($this->config);
        $this->model = new EstadisticaModel($this->dbManager);
        
        echo "DEBUG: Intentando conectar a la BD...<br>"; // <-- Nueva línea de depuración
        
        // Requisito: Establecer conexión
        if (!$this->dbManager->connect($driver)) {
             // Si falla, configuramos el mensaje de error y nos detenemos aquí.
             echo "DEBUG: !!! FALLO CRÍTICO EN LA CONEXIÓN A BD !!!<br>"; // <-- Nueva línea de depuración
             $error_message = "Error de conexión: Por favor, verifique sus credenciales (nombre de usuario, contraseña, nombre de BD, servidor).";
        } else {
             // Si tiene éxito, lo reportamos.
             echo "DEBUG: Conexión a BD EXITOSA.<br>"; // <-- Nueva línea de depuración
        }
        
        $driver = $_POST['driver'] ?? 'mysql';
        $this->driver = $driver;
        $table = $_POST['table'] ?? '';
        $column = $_POST['column'] ?? '';
        $error_message = null;
        $resultados = null;

        $this->dbManager = new DbManager($this->config);
        $this->model = new EstadisticaModel($this->dbManager);

        // El resto de la lógica de conexión y validación permanece igual...

        if (!$this->dbManager->connect($driver)) {
            $error_message = "Error de conexión: Por favor, verifique sus credenciales.";
        }

        if (empty($table) || empty($column)) {
            $error_message = "Debe seleccionar una tabla y una columna.";
        }

        if (!$error_message) {
            $resultados = $this->model->calcularEstadisticas($table, $column);

            if (isset($resultados['error'])) {
                $error_message = $resultados['error'];
                $resultados = null;
            }
        }

        // Volver a cargar la vista: obtener tablas y columnas actualizadas
        $connection_successful = $this->dbManager->connect($driver);
        $tables = $connection_successful ? $this->getTables() : ['error' => 'No se pudo conectar para listar las tablas.'];

        // OBTENER LA LISTA DE COLUMNAS para que el select se muestre correctamente
        $columns = [];
        if ($connection_successful && !empty($table)) {
            $columns = $this->getColumns($table);
        }

        // Mantener el estado de la tabla y columna seleccionadas para la vista
        $selected_table = $table;
        $selected_column = $column;

        include __DIR__ . '/../../views/index.php';
    }
}
