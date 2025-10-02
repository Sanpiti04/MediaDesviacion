<?php
// app/Models/DbManager.php

/**
 * Clase DbManager (POO): Gestiona la conexión a la base de datos (BD).
 * Encapsula la configuración y el objeto PDO para ser reutilizado de forma segura.
 */
class DbManager
{ // <-- INICIO DE LA CLASE (POO: Encapsulación)
    // Propiedades privadas para almacenar los detalles de la conexión
    private $host;
    private $port;
    private $dbName;
    private $user;
    private $password;
    private $conn; // Objeto PDO de conexión

    public function __construct(array $config)
    { // <-- MÉTODO CONSTRUCTOR (POO)
        // Inicializa las propiedades con los datos de configuración
        $this->host = $config['host'];
        $this->port = $config['port'];
        $this->dbName = $config['dbName'];
        $this->user = $config['user'];
        $this->password = $config['password'];
    }

    /**
     * Establece y verifica la conexión a la BD.
     * Cumple el REQUISITO DE COMPATIBILIDAD (MySQL, PostgreSQL, SQL Server).
     */
    public function connect(string $driver = 'mysql'): bool
    {
        try {
            switch (strtolower($driver)) {
                case 'mysql':
                    $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->dbName}";
                    break;
                case 'pgsql':
                    $dsn = "pgsql:host={$this->host};port={$this->port};dbname={$this->dbName}";
                    break;
                case 'sqlsrv':
                    $dsn = "sqlsrv:Server={$this->host},{$this->port};Database={$this->dbName}";
                    break;
                default:
                    throw new Exception("Driver de base de datos no compatible.");
            }

            // Crea una nueva instancia de PDO (POO)
            $this->conn = new PDO($dsn, $this->user, $this->password);
            // Configura PDO para lanzar excepciones en caso de error (REQUISITO DE SEGURIDAD/MANEJO DE ERRORES)
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return true;
        } catch (PDOException $e) {
            // REQUISITO DE MANEJO DE ERRORES: Registra el error de conexión
            error_log("Error de Conexión a BD: " . $e->getMessage());
            return false;
        } catch (Exception $e) {
            error_log("Error de Driver: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Retorna la instancia de conexión PDO.
     * POO: Permite que otros modelos utilicen la conexión establecida.
     */
    public function getConnection(): ?PDO
    {
        return $this->conn;
    }
}
