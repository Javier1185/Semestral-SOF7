<?php

class Conexion
{
    private static ?Conexion $instancia = null;
    private PDO $pdo;

    // Configuración de la base de datos
    private string $host = '127.0.0.1';
    private string $baseDatos = 'sistema_contable';
    private string $usuario = 'root';
    private string $contrasena = '';
    private string $charset = 'utf8mb4';
    private int $puerto = 3307;

    // Constructor privado (Singleton)
    private function __construct()
    {
        $dsn = "mysql:host={$this->host};port={$this->puerto};dbname={$this->baseDatos};charset={$this->charset}";

        $opciones = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {

            $this->pdo = new PDO(
                $dsn,
                $this->usuario,
                $this->contrasena,
                $opciones
            );

        } catch (PDOException $e) {

            die('Error de conexión: ' . $e->getMessage());

        }
    }

    // Obtiene la única instancia de la clase
    public static function obtenerInstancia(): Conexion
    {
        if (self::$instancia === null) {
            self::$instancia = new Conexion();
        }

        return self::$instancia;
    }

    // Devuelve el objeto PDO
    public function obtenerPDO(): PDO
    {
        return $this->pdo;
    }

    // Evita clonar la instancia
    private function __clone() {}

    // Evita deserializar la instancia
    public function __wakeup()
    {
        throw new Exception('No se puede deserializar un singleton.');
    }
}
?>
